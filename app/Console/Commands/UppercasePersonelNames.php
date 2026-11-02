<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UppercasePersonelNames extends Command
{
    protected $signature = 'pbvsi:uppercase-personel';
    protected $description = 'Standarisasi seluruh nama personel PBVSI ke UPPERCASE';

    public function handle()
    {
        $this->info("=== Mengonversi Nama Personel ke UPPERCASE ===");

        // 1. member_pemain
        $this->info("1. Mengonversi member_pemain...");
        $pemainUpdated = DB::update("
            UPDATE member_pemain 
            SET 
                nama_lengkap = UPPER(TRIM(nama_lengkap)),
                nama_punggung = CASE 
                    WHEN nama_punggung IS NOT NULL AND nama_punggung != '' 
                    THEN UPPER(TRIM(nama_punggung)) 
                    ELSE nama_punggung 
                END,
                tempat_lahir = CASE 
                    WHEN tempat_lahir IS NOT NULL AND tempat_lahir != '' 
                    THEN UPPER(TRIM(tempat_lahir)) 
                    ELSE tempat_lahir 
                END
        ");
        $this->info("Pemain updated: {$pemainUpdated} baris.");

        // 2. member_official
        $this->info("2. Mengonversi member_official...");
        $officialUpdated = DB::update("
            UPDATE member_official 
            SET 
                nama_lengkap = UPPER(TRIM(nama_lengkap)),
                jabatan_official = UPPER(TRIM(jabatan_official))
        ");
        $this->info("Official updated: {$officialUpdated} baris.");

        // 3. core_users (nama akun user: pemain, pelatih, wasit, official, admin)
        $this->info("3. Mengonversi core_users...");
        $usersUpdated = DB::update("
            UPDATE core_users 
            SET name = UPPER(TRIM(name))
            WHERE name IS NOT NULL AND name != ''
        ");
        $this->info("Core Users updated: {$usersUpdated} baris.");

        // 4. core_user_profiles
        $this->info("4. Mengonversi core_user_profiles...");
        $profilesUpdated = DB::update("
            UPDATE core_user_profiles 
            SET 
                tempat_lahir = CASE 
                    WHEN tempat_lahir IS NOT NULL AND tempat_lahir != '' 
                    THEN UPPER(TRIM(tempat_lahir)) 
                    ELSE tempat_lahir 
                END
        ");
        $this->info("User profiles updated: {$profilesUpdated} baris.");

        // 5. trn_official jika ada
        if (DB::getSchemaBuilder()->hasTable('trn_official')) {
            $this->info("5. Mengonversi trn_official...");
            $trnOfficialUpdated = DB::update("
                UPDATE trn_official 
                SET nama_lengkap = UPPER(TRIM(nama_lengkap))
            ");
            $this->info("Turnamen official updated: {$trnOfficialUpdated} baris.");
        }

        $this->info("=== Selesai! Seluruh nama personel berhasil diubah ke UPPERCASE ===");
    }
}
