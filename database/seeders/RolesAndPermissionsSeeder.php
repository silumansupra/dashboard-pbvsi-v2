<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'superadmin', 'guard_name' => 'web', 'display_name' => 'Superadmin PP PBVSI', 'description' => 'Akses penuh seluruh modul dan turnamen se-Indonesia'],
            ['name' => 'admin_kejurnas', 'guard_name' => 'web', 'display_name' => 'Panitia Pelaksana Kejurnas', 'description' => 'Akses manajemen operasional turnamen edisi tertentu'],
            ['name' => 'admin_pengprov', 'guard_name' => 'web', 'display_name' => 'Pengurus Provinsi PBVSI', 'description' => 'Akses verifikasi klub, pemain, dan rekomendasi daerah'],
            ['name' => 'admin_pengkot', 'guard_name' => 'web', 'display_name' => 'Pengurus Kota/Kabupaten PBVSI', 'description' => 'Akses administrasi tingkat kota/kabupaten'],
            ['name' => 'admin_klub', 'guard_name' => 'web', 'display_name' => 'Manajemen Tim / Klub Voli', 'description' => 'Akses registrasi roster dan dokumen tim'],
            ['name' => 'pemain', 'guard_name' => 'web', 'display_name' => 'Pemain Bola Voli', 'description' => 'Akses atlet PBVSI'],
            ['name' => 'official', 'guard_name' => 'web', 'display_name' => 'Official Tim', 'description' => 'Manajer, pelatih fisik, medis, dsb.'],
            ['name' => 'wasit', 'guard_name' => 'web', 'display_name' => 'Wasit Resmi PBVSI', 'description' => 'Akses profil perwasitan, penugasan, dan sertifikat'],
            ['name' => 'pelatih', 'guard_name' => 'web', 'display_name' => 'Pelatih Resmi PBVSI', 'description' => 'Akses lisensi kepelatihan dan sertifikasi'],
            ['name' => 'verifikator', 'guard_name' => 'web', 'display_name' => 'Verifikator Berkas', 'description' => 'Verifikator dokumen kejuaraan'],
            ['name' => 'petugas_tiket', 'guard_name' => 'web', 'display_name' => 'Petugas Gate Tiket', 'description' => 'Akses scan barcode tiket pertandingan'],
        ];

        foreach ($roles as $r) {
            DB::table('core_roles')->updateOrInsert(
                ['name' => $r['name'], 'guard_name' => $r['guard_name']],
                ['display_name' => $r['display_name'], 'description' => $r['description'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
