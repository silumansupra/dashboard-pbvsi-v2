<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Pemain;
use App\Models\Pengprov;

class SyncPersonelData extends Command
{
    protected $signature = 'pbvsi:sync-personel';
    protected $description = 'Sync Wasit, Pelatih, Official and enrich Pemain from legacy data';

    public function handle()
    {
        $this->info("=== Starting Personel Sync ===");

        // 1. Enrich Pemain with Pengprov & DOB
        $this->info("1. Enriching Pemain...");
        $affected = DB::statement("
            UPDATE member_pemain mp
            JOIN dev_dashpbvsi.tb_pemain_tim pt ON mp.user_id = pt.userid
            JOIN dev_dashpbvsi.tb_tim t ON pt.id_tim = t.id_tim
            JOIN org_pengprov p ON t.id_propinsi = p.provinsi_id
            SET mp.pengprov_id = p.id
            WHERE mp.pengprov_id IS NULL
        ");
        $this->info("Pemain Pengprov mapped!");

        DB::statement("
            UPDATE member_pemain mp
            JOIN dev_dashpbvsi.tb_import_player ip ON TRIM(LOWER(mp.nama_lengkap)) = TRIM(LOWER(ip.passport_name))
            SET 
                mp.tanggal_lahir = CASE 
                    WHEN mp.tanggal_lahir IS NOT NULL THEN mp.tanggal_lahir
                    WHEN CAST(ip.birthday AS CHAR) > '1950-01-01' THEN ip.birthday
                    ELSE NULL
                END,
                mp.posisi_utama = COALESCE(mp.posisi_utama, NULLIF(ip.position, '')),
                mp.nama_punggung = COALESCE(mp.nama_punggung, NULLIF(ip.shirt_name, ''))
            WHERE (CAST(ip.birthday AS CHAR) > '1950-01-01') 
               OR (ip.position IS NOT NULL AND ip.position != '')
               OR (ip.shirt_name IS NOT NULL AND ip.shirt_name != '')
        ");
        $this->info("Pemain details enriched from tb_import_player!");

        // 2. Sync Wasit
        $this->info("2. Syncing Wasit...");
        $wasitUsers = DB::select("
            SELECT u.id, u.name, u.username, up.nik, up.provinsi_id, p.id as pengprov_id
            FROM core_users u
            JOIN core_model_has_roles mhr ON u.id = mhr.model_id
            JOIN core_roles r ON mhr.role_id = r.id AND r.name = 'wasit'
            LEFT JOIN core_user_profiles up ON u.id = up.user_id
            LEFT JOIN org_pengprov p ON up.provinsi_id = p.provinsi_id
            WHERE u.id NOT IN (SELECT user_id FROM member_wasit)
        ");

        $volipantaiNames = DB::table('dev_dashpbvsi.migrasi_wasit_volipantai')
            ->pluck('nama_lengkap')
            ->map(fn($n) => strtolower(trim($n)))
            ->toArray();

        foreach ($wasitUsers as $wu) {
            $isPantai = in_array(strtolower(trim($wu->name)), $volipantaiNames);
            DB::table('member_wasit')->insert([
                'user_id' => $wu->id,
                'pengprov_id' => $wu->pengprov_id,
                'nomor_lisensi_wasit' => 'WASIT-' . str_pad($wu->id, 5, '0', STR_PAD_LEFT),
                'kategori_wasit' => $isPantai ? 'VOLI_PANTAI' : 'INDOOR',
                'tingkat_wasit' => 'Nasional',
                'status_aktif' => 1,
                'catatan_perwasitan' => 'Wasit Resmi PBVSI',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->info("Total wasit now: " . DB::table('member_wasit')->count());

        // 3. Sync Pelatih
        $this->info("3. Syncing Pelatih...");
        $coachImports = DB::select("
            SELECT io.passport_name, io.title, t.id_propinsi, p.id as pengprov_id
            FROM dev_dashpbvsi.tb_import_official io
            LEFT JOIN dev_dashpbvsi.tb_import_tim it ON io.id_import = it.id_import
            LEFT JOIN dev_dashpbvsi.tb_tim t ON it.id_tim = t.id_tim
            LEFT JOIN org_pengprov p ON t.id_propinsi = p.provinsi_id
            WHERE io.title LIKE '%COACH%' OR io.title LIKE '%TRAINER%'
        ");

        $pelatihRoleId = DB::table('core_roles')->where('name', 'pelatih')->value('id');

        $seenCoaches = [];
        foreach ($coachImports as $c) {
            $name = trim($c->passport_name);
            if (empty($name) || isset($seenCoaches[strtolower($name)])) {
                continue;
            }
            $seenCoaches[strtolower($name)] = true;

            // Check if user already exists
            $user = DB::table('core_users')->whereRaw('TRIM(LOWER(name)) = ?', [strtolower($name)])->first();
            if (!$user) {
                $userId = DB::table('core_users')->insertGetId([
                    'uuid' => (string) Str::uuid(),
                    'username' => '#plt' . rand(1000000, 9999999),
                    'name' => $name,
                    'email' => 'plt_' . Str::slug($name, '') . rand(100, 999) . '@pbvsi.or.id',
                    'password' => bcrypt('pbvsi12345'),
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $userId = $user->id;
            }

            // Assign role if not exists
            if ($pelatihRoleId) {
                $hasRole = DB::table('core_model_has_roles')
                    ->where('role_id', $pelatihRoleId)
                    ->where('model_id', $userId)
                    ->exists();
                if (!$hasRole) {
                    DB::table('core_model_has_roles')->insert([
                        'role_id' => $pelatihRoleId,
                        'model_type' => 'App\Models\User',
                        'model_id' => $userId,
                    ]);
                }
            }

            // Check if already in member_pelatih
            $inMember = DB::table('member_pelatih')->where('user_id', $userId)->exists();
            if (!$inMember) {
                $tingkat = 'Nasional B';
                if (stripos($c->title, 'HEAD') !== false) {
                    $tingkat = 'Nasional A';
                } elseif (stripos($c->title, '2') !== false) {
                    $tingkat = 'Nasional C';
                }

                DB::table('member_pelatih')->insert([
                    'user_id' => $userId,
                    'pengprov_id' => $c->pengprov_id,
                    'nomor_lisensi_pelatih' => 'PLT-' . str_pad($userId, 5, '0', STR_PAD_LEFT),
                    'tingkat_pelatih' => $tingkat,
                    'status_aktif' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        $this->info("Total pelatih now: " . DB::table('member_pelatih')->count());

        // 4. Sync Official
        $this->info("4. Syncing Official...");
        $officialUsers = DB::select("
            SELECT u.id, u.name, u.username, up.nik, up.jenis_kelamin, up.provinsi_id, p.id as pengprov_id
            FROM core_users u
            JOIN core_model_has_roles mhr ON u.id = mhr.model_id
            JOIN core_roles r ON mhr.role_id = r.id AND r.name = 'official'
            LEFT JOIN core_user_profiles up ON u.id = up.user_id
            LEFT JOIN org_pengprov p ON up.provinsi_id = p.provinsi_id
            WHERE u.id NOT IN (SELECT user_id FROM member_official)
        ");

        foreach ($officialUsers as $ou) {
            DB::table('member_official')->insert([
                'user_id' => $ou->id,
                'pengprov_id' => $ou->pengprov_id,
                'nama_lengkap' => $ou->name,
                'nik' => $ou->nik,
                'jenis_kelamin' => $ou->jenis_kelamin ?? 'L',
                'jabatan_official' => 'MANAGER',
                'nomor_lisensi' => 'OFF-' . str_pad($ou->id, 5, '0', STR_PAD_LEFT),
                'status_aktif' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->info("Total official now: " . DB::table('member_official')->count());

        $this->info("=== Sync Completed Successfully ===");
    }
}
