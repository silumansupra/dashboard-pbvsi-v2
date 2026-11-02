<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncBunnyPhotos extends Command
{
    protected $signature = 'pbvsi:sync-photos';
    protected $description = 'Strictly sync legacy photos from dev_dashpbvsi, Kejurnas, and Livoli using NIK and Name+Gender validation';

    public function handle(): int
    {
        $this->info("=== Memulai Sinkronisasi Foto Personel Ketat (Strict Identity Guard) ===");

        // 1. Reset foto lama yang tertukar dari folder ID kejurnas / livoli
        $this->info("1. Mereset foto yang salah tautan (kejurnas / livoli berbasis folder ID)...");
        $resetPemain = DB::table('member_pemain')
            ->where('foto_profil_path', 'like', 'kejurnas/%')
            ->orWhere('foto_profil_path', 'like', 'livoli/%')
            ->update(['foto_profil_path' => null]);
        
        $resetUsers = DB::table('core_users')
            ->where('avatar_path', 'like', 'kejurnas/%')
            ->orWhere('avatar_path', 'like', 'livoli/%')
            ->update(['avatar_path' => null]);

        $this->line("   - {$resetPemain} foto pemain direset.");
        $this->line("   - {$resetUsers} avatar user direset.");

        // 2. Sinkronisasi dari database PBVSI utama (dev_dashpbvsi)
        $this->info("2. Menyinkronkan data dari PBVSI Utama (dev_dashpbvsi)...");

        // 2a. tb_userinfo (Admin, Wasit, Pengprov foto_profil)
        $userinfo = DB::select("
            SELECT userid, nama_lengkap, gender, foto_profil 
            FROM dev_dashpbvsi.tb_userinfo 
            WHERE foto_profil IS NOT NULL AND foto_profil != ''
        ");
        $countUi = 0;
        foreach ($userinfo as $ui) {
            $path = "pbvsi/uploads/images/profil/{$ui->userid}/{$ui->foto_profil}";
            DB::table('core_users')
                ->where('id', $ui->userid)
                ->update(['avatar_path' => $path]);
            $countUi++;
        }
        $this->line("   - {$countUi} avatar dari tb_userinfo disinkronkan ke core_users.");

        // 2b. tb_userfile (Berkas foto verifikasi atlet di dev_dashpbvsi)
        // Pastikan hanya memasukkan yang gender-nya cocok (L -> PA, P -> PI)
        $userfiles = DB::select("
            SELECT uf.userid, uf.file_foto, ui.gender as legacy_gender, ui.nama_lengkap
            FROM dev_dashpbvsi.tb_userfile uf
            JOIN dev_dashpbvsi.tb_userinfo ui ON uf.userid = ui.userid
            WHERE uf.file_foto IS NOT NULL AND uf.file_foto != ''
        ");
        $countUf = 0;
        foreach ($userfiles as $uf) {
            $expectedGender = ($uf->legacy_gender === 'P') ? 'PI' : (($uf->legacy_gender === 'L') ? 'PA' : null);

            $query = DB::table('member_pemain')->where('user_id', $uf->userid);
            if ($expectedGender) {
                $query->where('jenis_kelamin', $expectedGender);
            }

            $affected = $query->update(['foto_profil_path' => $uf->file_foto]);
            if ($affected) {
                $countUf += $affected;
            }
        }
        $this->line("   - {$countUf} berkas foto dari tb_userfile disinkronkan ke member_pemain.");

        // 3. Sinkronisasi dari Dump Kejurnas U-19 & Livoli dengan Validasi Identitas Ketat
        $this->info("3. Memproses dump Kejurnas U-19 & Livoli dengan STRICT IDENTITY MATCH...");
        
        $kejurnasDump = 'D:/Dataku/DataLukman/Kerjoan-2/pbvsi/Kejurnas/KejurnasU19/u8873761_kejurnasu19.sql';
        $livoliDump   = 'D:/Dataku/DataLukman/Kerjoan-2/pbvsi/Livoli/u8873761_livoli.sql';

        $kejurnasProfiles = $this->parseProfiles($kejurnasDump, 'kejurnas');
        $livoliProfiles   = $this->parseProfiles($livoliDump, 'livoli');

        $this->line("   - Ditemukan " . count($kejurnasProfiles) . " profil berfoto di Kejurnas U-19 dump.");
        $this->line("   - Ditemukan " . count($livoliProfiles) . " profil berfoto di Livoli dump.");

        $allTournamentProfiles = array_merge($kejurnasProfiles, $livoliProfiles);

        $matchedPemain = 0;
        $matchedOfficial = 0;
        $matchedPelatih = 0;
        $rejectedGender = 0;

        foreach ($allTournamentProfiles as $prof) {
            $expectedPemainGender = ($prof['gender'] === 'P') ? 'PI' : (($prof['gender'] === 'L') ? 'PA' : null);
            $expectedOfficialGender = ($prof['gender'] === 'P') ? 'P' : (($prof['gender'] === 'L') ? 'L' : null);

            // A. COCOKKAN KE MEMBER_PEMAIN
            $pemain = null;
            // 1. Cek NIK valid
            if (!empty($prof['nik']) && strlen($prof['nik']) >= 12 && !preg_match('/^0+$/', $prof['nik'])) {
                $pemain = DB::table('member_pemain')->where('nik', $prof['nik'])->first();
                if ($pemain && $expectedPemainGender && $pemain->jenis_kelamin !== $expectedPemainGender) {
                    $rejectedGender++;
                    $pemain = null; // Batalkan jika gender berlawanan!
                }
            }

            // 2. Cek Nama Lengkap + Gender
            if (!$pemain && !empty($prof['nama']) && strlen($prof['nama']) > 3 && $expectedPemainGender) {
                $pemain = DB::table('member_pemain')
                    ->where('nama_lengkap', $prof['nama'])
                    ->where('jenis_kelamin', $expectedPemainGender)
                    ->first();
            }

            if ($pemain) {
                // Update pemain foto
                DB::table('member_pemain')
                    ->where('id', $pemain->id)
                    ->update(['foto_profil_path' => $prof['full_path']]);

                if ($pemain->user_id) {
                    DB::table('core_users')
                        ->where('id', $pemain->user_id)
                        ->update(['avatar_path' => $prof['full_path']]);
                }
                $matchedPemain++;
            }

            // B. COCOKKAN KE MEMBER_OFFICIAL
            $official = null;
            if (!empty($prof['nik']) && strlen($prof['nik']) >= 12 && !preg_match('/^0+$/', $prof['nik'])) {
                $official = DB::table('member_official')->where('nik', $prof['nik'])->first();
                if ($official && $expectedOfficialGender && $official->jenis_kelamin !== $expectedOfficialGender) {
                    $rejectedGender++;
                    $official = null;
                }
            }
            if (!$official && !empty($prof['nama']) && strlen($prof['nama']) > 3) {
                $qOff = DB::table('member_official')->where('nama_lengkap', $prof['nama']);
                if ($expectedOfficialGender) {
                    $qOff->where('jenis_kelamin', $expectedOfficialGender);
                }
                $official = $qOff->first();
            }

            if ($official && $official->user_id) {
                DB::table('core_users')
                    ->where('id', $official->user_id)
                    ->update(['avatar_path' => $prof['full_path']]);
                $matchedOfficial++;
            }

            // C. COCOKKAN KE MEMBER_PELATIH
            if (!empty($prof['nama']) && strlen($prof['nama']) > 3) {
                $pelatihUser = DB::table('member_pelatih')
                    ->join('core_users', 'core_users.id', '=', 'member_pelatih.user_id')
                    ->where('core_users.name', $prof['nama'])
                    ->value('member_pelatih.user_id');

                if ($pelatihUser) {
                    DB::table('core_users')
                        ->where('id', $pelatihUser)
                        ->update(['avatar_path' => $prof['full_path']]);
                    $matchedPelatih++;
                }
            }
        }

        $this->info("=== SINKRONISASI SELESAI DENGAN AMAN ===");
        $this->line("   - Pemain berhasil dicocokkan (NIK / Nama + Gender): {$matchedPemain}");
        $this->line("   - Official berhasil dicocokkan: {$matchedOfficial}");
        $this->line("   - Pelatih berhasil dicocokkan: {$matchedPelatih}");
        $this->line("   - Potensi tertukar gender yang berhasil dicegah: {$rejectedGender}");

        return Command::SUCCESS;
    }

    /**
     * Parse file SQL dump untuk tabel tb_user_profile
     */
    protected function parseProfiles(string $filePath, string $sourcePrefix): array
    {
        if (!file_exists($filePath)) {
            return [];
        }

        $handle = fopen($filePath, 'r');
        $profiles = [];

        while (($line = fgets($handle)) !== false) {
            if (str_contains($line, "INSERT INTO `tb_user_profile`")) {
                preg_match_all("/\((\d+),\s*'([^']*)',\s*'([^']*)',\s*(NULL|'[^']*'),\s*(NULL|'[^']*'),\s*(NULL|'[^']*'),\s*'([^']*)',\s*(NULL|'[^']*')/", $line, $matches, PREG_SET_ORDER);
                foreach ($matches as $m) {
                    $uid    = (int) $m[1];
                    $nik    = trim($m[2]);
                    $nama   = strtoupper(trim($m[3]));
                    $gender = ($m[4] !== 'NULL') ? trim($m[4], "'") : null;
                    $dob    = ($m[5] !== 'NULL') ? trim($m[5], "'") : null;
                    $foto   = trim($m[8], "'");

                    if ($foto !== 'NULL' && !empty($foto)) {
                        $profiles[] = [
                            'source'    => $sourcePrefix,
                            'user_id'   => $uid,
                            'nik'       => $nik,
                            'nama'      => $nama,
                            'gender'    => $gender,
                            'dob'       => $dob,
                            'foto'      => $foto,
                            'full_path' => "{$sourcePrefix}/uploads/images/profil/{$uid}/{$foto}",
                        ];
                    }
                }
            }
        }

        fclose($handle);
        return $profiles;
    }
}
