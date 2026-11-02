<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MigrateTournamentHistory extends Command
{
    protected $signature = 'pbvsi:migrate-tournaments {--fresh : Reset participant teams and rosters before migration}';
    protected $description = 'Migrate historical tournaments, deduplicate teams into org_klub, map to trn_peserta_tim, trn_roster, and trn_official';

    // Map old id_turnamen to [master_slug, tahun, is_klub]
    protected array $tournamentMap = [
        1  => ['kapolri-cup', 2023, false],
        2  => ['livoli-divisi-1', 2023, true],
        3  => ['livoli-divisi-utama', 2023, true],
        4  => ['kejurnas-u19', 2022, false], // Kejuaraan Nasional Junior 2022 (Antar Pengprov)
        5  => ['kejurnas-u17', 2022, true],  // Kejuaraan Nasional U-17 2022 (Antar Klub)
        6  => ['kejurnas-u19', 2023, false], // Kejuaraan Nasional Junior 2023 (Antar Pengprov)
        7  => ['kejurnas-u17', 2023, true],  // Kejuaraan Nasional U-17 2023 (Antar Klub)
        8  => ['sirkuit-nasional-voli-pantai', 2023, false],
        9  => ['kejurnas-u17', 2024, true],
        11 => ['livoli-divisi-1', 2024, true],
        12 => ['livoli-divisi-utama', 2024, true],
        13 => ['kejurnas-u19', 2024, true],
        14 => ['kejurnas-u16', 2025, true],
        15 => ['kejurnas-u17', 2025, true],
        16 => ['kejurnas-u19', 2025, true],
        18 => ['kejurnas-u18', 2026, true],
    ];

    public function handle()
    {
        $this->info("=========================================================");
        $this->info("   PBVSI TOURNAMENT & ROSTER MIGRATION ENGINE (V1 -> V2)");
        $this->info("=========================================================");

        if ($this->option('fresh')) {
            $this->warn("Option --fresh specified. Clearing old tournament participant and roster data...");
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('trn_lineup_otwo')->truncate();
            DB::table('trn_roster')->truncate();
            DB::table('trn_official')->truncate();
            DB::table('trn_peserta_tim')->truncate();
            DB::table('sys_migration_maps')->whereIn('target_table', ['trn_peserta_tim', 'trn_roster', 'trn_official', 'org_klub'])->delete();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->info("Tables cleared.");
        }

        // STEP 1: Ensure Editions (trn_edisi)
        $this->info("\n--- STEP 1: Ensuring Annual Editions (trn_edisi) ---");
        $edisiCache = $this->ensureEditions();

        // STEP 2: Deduplicate & Seed Master Clubs (org_klub)
        $this->info("\n--- STEP 2: Deduplicating & Seeding Master Clubs (org_klub) ---");
        $klubCache = $this->seedMasterClubs();

        // STEP 3: Migrate Tournament Participant Teams (trn_peserta_tim)
        $this->info("\n--- STEP 3: Migrating Tournament Participant Teams (trn_peserta_tim) ---");
        $pesertaTimMap = $this->migrateParticipantTeams($edisiCache, $klubCache);

        // STEP 4: Ensure Player Bank & Migrate Rosters (trn_roster)
        $this->info("\n--- STEP 4: Migrating Player Rosters (trn_roster) ---");
        $this->migrateRosters($pesertaTimMap);

        // STEP 5: Migrate Team Officials (trn_official)
        $this->info("\n--- STEP 5: Migrating Team Officials (trn_official) ---");
        $this->migrateOfficials($pesertaTimMap);

        $this->info("\n=========================================================");
        $this->info("   MIGRATION COMPLETED SUCCESSFULLY!");
        $this->info("=========================================================");
        $this->printSummary();

        return 0;
    }

    /**
     * Ensure all historical editions exist in trn_edisi
     */
    protected function ensureEditions(): array
    {
        $edisiCache = [];
        $masters = DB::table('trn_master_turnamen')->get()->keyBy('slug');

        foreach ($this->tournamentMap as $oldId => [$slug, $tahun, $isKlub]) {
            if (!isset($masters[$slug])) {
                $this->error("Master tournament with slug '$slug' not found!");
                continue;
            }

            $master = $masters[$slug];
            $slugEdisi = "{$slug}-{$tahun}";

            $edisi = DB::table('trn_edisi')->where('slug_edisi', $slugEdisi)->first();

            if (!$edisi) {
                $status = ($tahun < 2026) ? 'FINISHED' : 'REGISTRASI';
                $namaEdisi = "{$master->nama_turnamen} {$tahun}";

                $edisiId = DB::table('trn_edisi')->insertGetId([
                    'turnamen_id' => $master->id,
                    'tahun' => $tahun,
                    'slug_edisi' => $slugEdisi,
                    'nama_edisi' => $namaEdisi,
                    'max_kuota_pemain' => 14,
                    'status_edisi' => $status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->line(" + Created edisi: $slugEdisi (ID: $edisiId)");
                $edisi = (object)['id' => $edisiId, 'slug_edisi' => $slugEdisi];
            }

            $edisiCache[$oldId] = $edisi->id;
        }

        $this->info("Total registered editions: " . count($edisiCache));
        return $edisiCache;
    }

    /**
     * Deduplicate legacy team names into distinct org_klub entries
     */
    protected function seedMasterClubs(): array
    {
        $klubCache = [];

        // Load existing clubs in org_klub
        $existing = DB::table('org_klub')->get();
        foreach ($existing as $ek) {
            $normalized = $this->normalizeClubName($ek->nama_klub);
            $klubCache[$normalized] = $ek->id;
        }

        // Get provinces lookup
        $pengprovMap = DB::table('org_pengprov')->pluck('id', 'provinsi_id')->toArray();

        // Get candidate club names from legacy database
        $clubTournamentIds = array_keys(array_filter($this->tournamentMap, fn($m) => $m[2] === true));

        $legacyClubs = DB::select("
            SELECT 
                TRIM(UPPER(nama_tim)) as nama_tim,
                MAX(id_propinsi) as id_propinsi,
                MAX(logo) as logo,
                MIN(created_at) as first_created
            FROM dev_dashpbvsi.tb_tim
            WHERE id_turnamen IN (" . implode(',', $clubTournamentIds) . ")
              AND nama_tim IS NOT NULL AND TRIM(nama_tim) != ''
            GROUP BY TRIM(UPPER(nama_tim))
            ORDER BY first_created ASC
        ");

        $insertedCount = 0;
        foreach ($legacyClubs as $lc) {
            $norm = $this->normalizeClubName($lc->nama_tim);
            if (empty($norm) || strlen($norm) < 2) {
                continue;
            }

            if (!isset($klubCache[$norm])) {
                $provId = (string) $lc->id_propinsi;
                $pengprovId = $pengprovMap[$provId] ?? null;

                $newKlubId = DB::table('org_klub')->insertGetId([
                    'nama_klub' => $norm,
                    'pengprov_id' => $pengprovId,
                    'logo_path' => $lc->logo,
                    'status_verifikasi' => 'VERIFIED',
                    'created_at' => $lc->first_created ?? now(),
                    'updated_at' => now(),
                ]);

                // Update unique code
                DB::table('org_klub')->where('id', $newKlubId)->update([
                    'kode_klub' => 'KLUB-' . str_pad($newKlubId, 5, '0', STR_PAD_LEFT)
                ]);

                $klubCache[$norm] = $newKlubId;
                $insertedCount++;
            }
        }

        $this->info("Distinct clubs loaded in master org_klub: " . count($klubCache) . " (newly seeded: $insertedCount)");
        return $klubCache;
    }

    /**
     * Migrate teams from dev_dashpbvsi.tb_tim into trn_peserta_tim
     */
    protected function migrateParticipantTeams(array $edisiCache, array $klubCache): array
    {
        $pesertaTimMap = [];
        $validTournaments = array_keys($this->tournamentMap);

        $pengprovMap = DB::table('org_pengprov')->pluck('id', 'provinsi_id')->toArray();
        $pengprovByName = [];
        foreach (DB::table('org_pengprov')->get() as $pp) {
            // "PENGPROV PBVSI JAWA TIMUR" -> "JAWA TIMUR"
            $shortName = trim(str_replace(['PENGPROV PBVSI', 'PENGPROV', 'PBVSI'], '', $pp->nama_pengprov));
            $pengprovByName[strtoupper($shortName)] = $pp->id;
        }

        $teams = DB::select("
            SELECT * FROM dev_dashpbvsi.tb_tim
            WHERE id_turnamen IN (" . implode(',', $validTournaments) . ")
            ORDER BY id_tim ASC
        ");

        $bar = $this->output->createProgressBar(count($teams));
        $bar->start();

        foreach ($teams as $t) {
            $oldTurnamenId = (int) $t->id_turnamen;
            $edisiId = $edisiCache[$oldTurnamenId] ?? null;
            if (!$edisiId) {
                $bar->advance();
                continue;
            }

            $isKlubTurnamen = $this->tournamentMap[$oldTurnamenId][2];
            $cleanName = strtoupper(trim($t->nama_tim));
            $normClubName = $this->normalizeClubName($t->nama_tim);

            $klubId = null;
            $provId = (string) $t->id_propinsi;
            $pengprovId = $pengprovMap[$provId] ?? null;

            if ($isKlubTurnamen) {
                $klubId = $klubCache[$normClubName] ?? null;
            } else {
                // If it's a regional contingent (Kapolri Cup / Kejurnas Junior)
                if (!$pengprovId) {
                    foreach ($pengprovByName as $provShort => $pid) {
                        if (str_contains($cleanName, $provShort)) {
                            $pengprovId = $pid;
                            break;
                        }
                    }
                }
            }

            $gender = in_array(strtoupper(trim((string)$t->equipe)), ['PA', 'PI']) ? strtoupper(trim((string)$t->equipe)) : 'PA';

            // Check if already migrated
            $existingPeserta = DB::table('trn_peserta_tim')
                ->where('edisi_id', $edisiId)
                ->where('nama_tim', $cleanName)
                ->where('kategori_gender', $gender)
                ->first();

            if ($existingPeserta) {
                $pesertaTimId = $existingPeserta->id;
            } else {
                $pesertaTimId = DB::table('trn_peserta_tim')->insertGetId([
                    'edisi_id' => $edisiId,
                    'klub_id' => $klubId,
                    'pengprov_id' => $pengprovId,
                    'nama_tim' => $cleanName,
                    'kode_tim' => $t->kode ?: null,
                    'kategori_gender' => $gender,
                    'warna_jersey_1' => $t->kostum_1 ?: null,
                    'warna_jersey_2' => $t->kostum_2 ?: null,
                    'warna_jersey_3' => $t->kostum_3 ?: null,
                    'warna_jersey_libero_1' => $t->lib_kostum_1 ?: null,
                    'warna_jersey_libero_2' => $t->lib_kostum_2 ?: null,
                    'file_jersey_1' => $t->file_kostum_1 ?: null,
                    'file_jersey_2' => $t->file_kostum_2 ?: null,
                    'file_jersey_3' => $t->file_kostum_3 ?: null,
                    'logo_tim_path' => $t->logo ?: null,
                    'foto_skuad_path' => $t->foto_tim ?: null,
                    'status_verifikasi' => 'VERIFIED',
                    'created_at' => $t->created_at ?: now(),
                    'updated_at' => $t->updated_at ?: now(),
                ]);

                // Map to sys_migration_maps
                DB::table('sys_migration_maps')->insert([
                    'source_database' => 'dev_dashpbvsi',
                    'source_table' => 'tb_tim',
                    'source_id' => (string) $t->id_tim,
                    'target_table' => 'trn_peserta_tim',
                    'target_id' => $pesertaTimId,
                    'status' => 'SUCCESS',
                    'notes' => "$cleanName (Edisi: $edisiId, Equipe: $gender)",
                    'migrated_at' => now(),
                ]);
            }

            $pesertaTimMap[$t->id_tim] = $pesertaTimId;
            $bar->advance();
        }

        $bar->finish();
        $this->info("\nMigrated participant teams (trn_peserta_tim): " . count($pesertaTimMap));
        return $pesertaTimMap;
    }

    /**
     * Migrate rosters from dev_dashpbvsi.tb_pemain_tim into trn_roster
     */
    protected function migrateRosters(array $pesertaTimMap): void
    {
        $oldTimIds = array_keys($pesertaTimMap);
        if (empty($oldTimIds)) {
            return;
        }

        // Cache member_pemain by user_id
        $pemainCache = DB::table('member_pemain')->pluck('id', 'user_id')->toArray();
        $playerRole = DB::table('core_roles')->where('name', 'pemain')->value('id');

        // Fetch distinct team players
        $rosters = DB::select("
            SELECT DISTINCT pt.id_tim, pt.userid
            FROM dev_dashpbvsi.tb_pemain_tim pt
            WHERE pt.id_tim IN (" . implode(',', $oldTimIds) . ")
              AND pt.userid IS NOT NULL
        ");

        $this->info("Processing " . count($rosters) . " distinct roster entries...");

        // Preload player physical attributes from dev_dashpbvsi.tb_pemain
        $rawAttributes = DB::select("
            SELECT 
                userid, tinggi, berat, hr_spike, hr_block, posisi, no_kostum, nama_kostum
            FROM dev_dashpbvsi.tb_pemain
            ORDER BY tinggi IS NOT NULL DESC, no_kostum IS NOT NULL DESC, id_pemain ASC
        ");

        $playerAttr = [];
        foreach ($rawAttributes as $attr) {
            if (!isset($playerAttr[$attr->userid])) {
                $playerAttr[$attr->userid] = $attr;
            }
        }

        // Track used jersey numbers per peserta_tim_id to avoid unique key conflicts
        $usedJerseyNumbers = [];
        $existingRosters = DB::table('trn_roster')->get(['peserta_tim_id', 'nomor_punggung', 'pemain_id']);
        foreach ($existingRosters as $er) {
            $usedJerseyNumbers[$er->peserta_tim_id][$er->nomor_punggung] = true;
        }

        $bar = $this->output->createProgressBar(count($rosters));
        $bar->start();

        $rosterInserts = [];
        $totalMigratedRoster = 0;

        foreach ($rosters as $r) {
            $pesertaTimId = $pesertaTimMap[$r->id_tim] ?? null;
            if (!$pesertaTimId) {
                $bar->advance();
                continue;
            }

            $userId = (int) $r->userid;
            $pemainId = $pemainCache[$userId] ?? null;

            // If player not yet in member_pemain, register them now
            if (!$pemainId) {
                $pemainId = $this->registerMissingPlayer($userId, $playerRole);
                if ($pemainId) {
                    $pemainCache[$userId] = $pemainId;
                }
            }

            if (!$pemainId) {
                $bar->advance();
                continue;
            }

            // Check if player is already rostered in this team
            $alreadyRostered = DB::table('trn_roster')
                ->where('peserta_tim_id', $pesertaTimId)
                ->where('pemain_id', $pemainId)
                ->exists();

            if ($alreadyRostered) {
                $bar->advance();
                continue;
            }

            // Determine attributes & jersey number
            $attr = $playerAttr[$userId] ?? null;
            $rawNo = $attr ? (int) $attr->no_kostum : 0;

            // Pick unique jersey number
            $assignedNo = $this->allocateJerseyNumber($pesertaTimId, $rawNo, $usedJerseyNumbers);
            $usedJerseyNumbers[$pesertaTimId][$assignedNo] = true;

            $posisiRaw = $attr ? strtoupper(trim((string)$attr->posisi)) : '';
            $posisi = $this->normalizePosition($posisiRaw);
            $isLibero = ($posisi === 'L');

            $tinggi = $attr ? $this->sanitizeFloatRange($attr->tinggi, 100.0, 240.0) : null;
            $berat = $attr ? $this->sanitizeFloatRange($attr->berat, 30.0, 160.0) : null;
            $spike = $attr ? $this->sanitizeIntRange($attr->hr_spike, 150, 420) : null;
            $block = $attr ? $this->sanitizeIntRange($attr->hr_block, 150, 400) : null;
            $shirtName = $attr && !empty($attr->nama_kostum) ? strtoupper(trim($attr->nama_kostum)) : null;

            $rosterInserts[] = [
                'peserta_tim_id' => $pesertaTimId,
                'pemain_id' => $pemainId,
                'nomor_punggung' => $assignedNo,
                'nama_jersey' => $shirtName,
                'posisi_bermain' => $posisi,
                'tinggi_cm' => $tinggi,
                'berat_kg' => $berat,
                'spike_reach_cm' => $spike,
                'block_reach_cm' => $block,
                'is_captain' => 0,
                'is_libero' => $isLibero ? 1 : 0,
                'status_verifikasi' => 'VERIFIED',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($rosterInserts) >= 500) {
                DB::table('trn_roster')->insert($rosterInserts);
                $totalMigratedRoster += count($rosterInserts);
                $rosterInserts = [];
            }

            $bar->advance();
        }

        if (!empty($rosterInserts)) {
            DB::table('trn_roster')->insert($rosterInserts);
            $totalMigratedRoster += count($rosterInserts);
        }

        $bar->finish();
        $this->info("\nTotal new roster rows inserted (trn_roster): " . $totalMigratedRoster);
    }

    /**
     * Migrate officials (head coach, ast coach, manager, therapist)
     */
    protected function migrateOfficials(array $pesertaTimMap): void
    {
        $oldTimIds = array_keys($pesertaTimMap);
        if (empty($oldTimIds)) {
            return;
        }

        $officialsCount = 0;

        // 1. From tb_tim columns
        $teams = DB::select("
            SELECT id_tim, head_coach, ast_coach_1, ast_coach_2, manager, ast_manager_1, therapist, translator
            FROM dev_dashpbvsi.tb_tim
            WHERE id_tim IN (" . implode(',', $oldTimIds) . ")
        ");

        // Preload userinfo for name resolution
        $userNames = DB::table('dev_dashpbvsi.tb_userinfo')
            ->whereNotNull('nama_lengkap')
            ->pluck('nama_lengkap', 'userid')
            ->toArray();

        $coreUserIds = DB::table('core_users')->pluck('id', 'id')->toArray();

        $roleColumns = [
            'head_coach'   => 'HEAD_COACH',
            'ast_coach_1'  => 'AST_COACH_1',
            'ast_coach_2'  => 'AST_COACH_2',
            'manager'      => 'MANAGER',
            'ast_manager_1'=> 'AST_MANAGER',
            'therapist'    => 'THERAPIST',
            'translator'   => 'TRANSLATOR',
        ];

        foreach ($teams as $t) {
            $pesertaTimId = $pesertaTimMap[$t->id_tim] ?? null;
            if (!$pesertaTimId) {
                continue;
            }

            foreach ($roleColumns as $col => $jabatanEnum) {
                $val = trim((string)$t->$col);
                if (empty($val)) {
                    continue;
                }

                $name = '';
                $userId = null;
                if (is_numeric($val)) {
                    $uid = (int) $val;
                    $name = $userNames[$uid] ?? '';
                    $userId = isset($coreUserIds[$uid]) ? $uid : null;
                } else {
                    $name = $val;
                }

                $name = strtoupper(trim($name));
                if (empty($name) || strlen($name) < 2) {
                    continue;
                }

                // Check if already registered
                $exists = DB::table('trn_official')
                    ->where('peserta_tim_id', $pesertaTimId)
                    ->where('jabatan', $jabatanEnum)
                    ->where('nama_lengkap', $name)
                    ->exists();

                if (!$exists) {
                    DB::table('trn_official')->insert([
                        'peserta_tim_id' => $pesertaTimId,
                        'user_id' => $userId,
                        'nama_lengkap' => $name,
                        'jabatan' => $jabatanEnum,
                        'status_verifikasi' => 'VERIFIED',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $officialsCount++;
                }
            }
        }

        // 2. From tb_import_official
        $importOfficials = DB::select("
            SELECT io.passport_name, io.title, io.license_id, it.id_tim
            FROM dev_dashpbvsi.tb_import_official io
            JOIN dev_dashpbvsi.tb_import_tim it ON io.id_import = it.id_import
            WHERE it.id_tim IN (" . implode(',', $oldTimIds) . ")
              AND io.passport_name IS NOT NULL AND TRIM(io.passport_name) != ''
        ");

        foreach ($importOfficials as $io) {
            $pesertaTimId = $pesertaTimMap[$io->id_tim] ?? null;
            if (!$pesertaTimId) {
                continue;
            }

            $name = strtoupper(trim($io->passport_name));
            $title = strtoupper(trim((string)$io->title));

            $jabatan = 'MANAGER';
            if (str_contains($title, 'HEAD COACH') || $title === 'COACH') {
                $jabatan = 'HEAD_COACH';
            } elseif (str_contains($title, 'ASSISTANT COACH 1') || str_contains($title, 'ASSISTANCT COACH 1')) {
                $jabatan = 'AST_COACH_1';
            } elseif (str_contains($title, 'ASSISTANT COACH 2')) {
                $jabatan = 'AST_COACH_2';
            } elseif (str_contains($title, 'THERAPIST')) {
                $jabatan = 'THERAPIST';
            } elseif (str_contains($title, 'TRAINER')) {
                $jabatan = 'TRAINER';
            }

            $exists = DB::table('trn_official')
                ->where('peserta_tim_id', $pesertaTimId)
                ->where('jabatan', $jabatan)
                ->where('nama_lengkap', $name)
                ->exists();

            if (!$exists) {
                DB::table('trn_official')->insert([
                    'peserta_tim_id' => $pesertaTimId,
                    'user_id' => null,
                    'nama_lengkap' => $name,
                    'jabatan' => $jabatan,
                    'nomor_lisensi' => $io->license_id ?: null,
                    'status_verifikasi' => 'VERIFIED',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $officialsCount++;
            }
        }

        $this->info("Total team officials migrated (trn_official): " . $officialsCount);
    }

    protected int $nextPlayerNum = 0;

    protected function generateUniquePlayerId(): string
    {
        if ($this->nextPlayerNum === 0) {
            $maxId = (int) DB::table('member_pemain')->max('id');
            $this->nextPlayerNum = max($maxId, 10000);
        }
        do {
            $this->nextPlayerNum++;
            $code = 'PBVSI-' . str_pad($this->nextPlayerNum, 6, '0', STR_PAD_LEFT);
        } while (DB::table('member_pemain')->where('pbvsi_player_id', $code)->exists());

        return $code;
    }

    /**
     * Auto register missing player into core_users & member_pemain
     */
    protected function registerMissingPlayer(int $userId, ?int $playerRole): ?int
    {
        $userInfo = DB::table('dev_dashpbvsi.tb_userinfo')->where('userid', $userId)->first();
        $userLegacy = DB::table('dev_dashpbvsi.tb_user')->where('userid', $userId)->first();

        $fullName = $userInfo && !empty($userInfo->nama_lengkap) ? trim($userInfo->nama_lengkap) : '';
        if (empty($fullName)) {
            return null;
        }

        $fullNameUpper = strtoupper($fullName);
        $nik = $userInfo->nik ?? '0000000000000000';
        $gender = ($userInfo->gender && strtoupper(trim($userInfo->gender)) === 'P') ? 'PI' : 'PA';
        $dob = ($userInfo->tgl_lahir && $userInfo->tgl_lahir > '1950-01-01') ? $userInfo->tgl_lahir : null;
        $pob = $userInfo->tempat_lahir ?? null;

        // Check if user exists in core_users
        $user = DB::table('core_users')->where('id', $userId)->first();
        if (!$user) {
            $email = $userLegacy->email ?? ($userId . '_player@pbvsi.or.id');
            if (DB::table('core_users')->where('email', $email)->exists()) {
                $email = $userId . '_' . rand(100, 999) . '@pbvsi.or.id';
            }

            $username = $userLegacy->username ?? ('#ply' . $userId);
            if (DB::table('core_users')->where('username', $username)->exists()) {
                $username = '#ply' . $userId . '_' . rand(10, 99);
            }

            DB::table('core_users')->insert([
                'id' => $userId,
                'uuid' => (string) Str::uuid(),
                'name' => $fullNameUpper,
                'username' => $username,
                'email' => $email,
                'password' => bcrypt('pbvsi12345'),
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($playerRole) {
                DB::table('core_model_has_roles')->insert([
                    'role_id' => $playerRole,
                    'model_type' => 'App\Models\User',
                    'model_id' => $userId,
                ]);
            }
        }

        $playerCode = $this->generateUniquePlayerId();

        // Insert into member_pemain
        $pemainId = DB::table('member_pemain')->insertGetId([
            'user_id' => $userId,
            'pbvsi_player_id' => $playerCode,
            'nik' => $nik,
            'nama_lengkap' => $fullNameUpper,
            'jenis_kelamin' => $gender,
            'tempat_lahir' => $pob,
            'tanggal_lahir' => $dob,
            'status_kewarganegaraan' => 'WNI_DAERAH',
            'status_verifikasi' => 'VERIFIED',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $pemainId;
    }

    /**
     * Clean and normalize club names
     */
    protected function normalizeClubName(?string $name): string
    {
        if (empty($name)) {
            return '';
        }
        $name = strtoupper(trim($name));
        // Remove trailing dots, hyphens, and whitespace
        $name = preg_replace('/[.\s\-_]+$/', '', $name);
        // Replace multiple whitespace with single space
        $name = preg_replace('/\s+/', ' ', $name);
        return trim($name);
    }

    /**
     * Normalize player position codes
     */
    protected function normalizePosition(string $pos): string
    {
        $pos = strtoupper(trim($pos));
        return match ($pos) {
            '1', 'L', 'LIBERO' => 'L',
            '2', 'MB', 'MIDDLE BLOCKER' => 'MB',
            '3', 'OH', 'OUTSIDE HITTER' => 'OH',
            '4', 'OP', 'OPPOSITE HITTER', 'OPPOSITE' => 'OP',
            '5', 'S', 'SETTER' => 'S',
            default => 'OH',
        };
    }

    /**
     * Allocate unique jersey number in range 1-99
     */
    protected function allocateJerseyNumber(int $pesertaTimId, int $preferredNo, array &$usedNumbers): int
    {
        if ($preferredNo >= 1 && $preferredNo <= 99 && !isset($usedNumbers[$pesertaTimId][$preferredNo])) {
            $usedNumbers[$pesertaTimId][$preferredNo] = true;
            return $preferredNo;
        }

        for ($i = 1; $i <= 99; $i++) {
            if (!isset($usedNumbers[$pesertaTimId][$i])) {
                $usedNumbers[$pesertaTimId][$i] = true;
                return $i;
            }
        }

        for ($i = 100; $i <= 999; $i++) {
            if (!isset($usedNumbers[$pesertaTimId][$i])) {
                $usedNumbers[$pesertaTimId][$i] = true;
                return $i;
            }
        }

        return rand(1000, 9999);
    }

    protected function sanitizeIntRange($val, int $min, int $max): ?int
    {
        if ($val === null || trim((string)$val) === '') {
            return null;
        }
        $val = trim((string)$val);
        // If it starts with 3 digits (e.g. 328305 -> 328)
        if (preg_match('/^(\d{3})/', $val, $m)) {
            $num = (int) $m[1];
            if ($num >= $min && $num <= $max) {
                return $num;
            }
        }
        if (is_numeric($val)) {
            $num = (int) $val;
            if ($num >= $min && $num <= $max) {
                return $num;
            }
        }
        return null;
    }

    protected function sanitizeFloatRange($val, float $min, float $max): ?float
    {
        if ($val === null || trim((string)$val) === '') {
            return null;
        }
        $val = trim((string)$val);
        if (is_numeric($val)) {
            $num = (float) $val;
            if ($num >= $min && $num <= $max) {
                return $num;
            }
        }
        return null;
    }

    /**
     * Print summary counts
     */
    protected function printSummary(): void
    {
        $klubCount = DB::table('org_klub')->count();
        $edisiCount = DB::table('trn_edisi')->count();
        $pesertaCount = DB::table('trn_peserta_tim')->count();
        $rosterCount = DB::table('trn_roster')->count();
        $officialCount = DB::table('trn_official')->count();
        $pemainCount = DB::table('member_pemain')->count();

        $this->table(
            ['Entity', 'Total Records in db_pbvsi_v2'],
            [
                ['Master Klub (org_klub)', number_format($klubCount)],
                ['Bank Pemain (member_pemain)', number_format($pemainCount)],
                ['Edisi Turnamen (trn_edisi)', number_format($edisiCount)],
                ['Tim Peserta (trn_peserta_tim)', number_format($pesertaCount)],
                ['Roster Atlet (trn_roster)', number_format($rosterCount)],
                ['Official & Pelatih (trn_official)', number_format($officialCount)],
            ]
        );
    }
}
