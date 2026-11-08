<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Role;
use App\Models\Permission;
use App\Models\PesertaTim;
use App\Services\MenuService;

class UserAccountsSeeder extends Seeder
{
    /**
     * Seed user accounts with default access by role:
     * - Superadmin: Akses penuh ke seluruh modul PBVSI Pusat, Master Data, KYC, dan Menu RBAC.
     * - Admin Tim: Akses ke portal administrasi tim (Dashboard, Jersey, Pemain, Official, Roster O-2, Cetak O-2, dan Profil).
     * - Wasit: Akses default ke Dashboard (Portal Anggota) dan Halaman Profil (modul sertifikat disiapkan untuk fase mendatang).
     * - Pelatih, Pemain, Official, Pengprov: Default akses ke Dashboard & Halaman Profil.
     * - Verifikator: Akses Dashboard, Profil, dan modul Verifikasi Berkas Identitas (KYC).
     */
    public function run(): void
    {
        $this->command?->info('Memulai seeding akun pengguna PBVSI...');

        // 1. Pastikan seluruh Role & Permission utama tersedia
        $this->call([
            RolesAndPermissionsSeeder::class,
            DynamicMenuAndPermissionSeeder::class,
        ]);

        $defaultPassword = 'password';
        $hashedPassword = Hash::make($defaultPassword);

        // 2. Daftar blueprint akun pengguna yang akan di-seed
        $accounts = [
            [
                'username' => 'superadmin',
                'name' => 'SUPERADMIN PP PBVSI',
                'email' => 'superadmin@pbvsi.or.id',
                'phone' => '081100000001',
                'role' => 'superadmin',
                'nik' => '3171010101010001',
                'jenis_kelamin' => 'L',
                'alamat' => 'Gedung Graha Pemuda Senayan, Jl. Gerbang Pemuda No. 3, Jakarta Pusat',
                'direct_permissions' => [],
                'description' => 'Superadministrator PBVSI Pusat (Akses Penuh)',
            ],
            [
                'username' => 'admintim',
                'name' => 'ADMINISTRATOR TIM KEJURNAS',
                'email' => 'admintim@pbvsi.or.id',
                'phone' => '081100000002',
                'role' => 'admin_tim',
                'nik' => '3171010101010002',
                'jenis_kelamin' => 'L',
                'alamat' => 'Sekretariat Kontingen Tim Bola Voli, Jakarta',
                'direct_permissions' => ['tim.manage'],
                'description' => 'Administrator Tim/Kontingen (Akses Data Tim & Roster O-2)',
            ],
            [
                'username' => 'wasit',
                'name' => 'WASIT NASIONAL PBVSI',
                'email' => 'wasit@pbvsi.or.id',
                'phone' => '081100000003',
                'role' => 'wasit',
                'nik' => '3171010101010003',
                'jenis_kelamin' => 'L',
                'alamat' => 'Komplek Gelora Bung Karno, Senayan, Jakarta Pusat',
                'direct_permissions' => ['sertifikat.view'],
                'description' => 'Wasit Resmi PBVSI (Default Dashboard & Profil, siap sertifikat)',
            ],
            [
                'username' => 'pelatih',
                'name' => 'PELATIH NASIONAL PBVSI',
                'email' => 'pelatih@pbvsi.or.id',
                'phone' => '081100000004',
                'role' => 'pelatih',
                'nik' => '3171010101010004',
                'jenis_kelamin' => 'L',
                'alamat' => 'Pusat Pelatihan PBVSI, Padepokan Bola Voli Jenderal Polisi Kunarto, Sentul',
                'direct_permissions' => ['sertifikat.view'],
                'description' => 'Pelatih Resmi PBVSI (Default Dashboard & Profil)',
            ],
            [
                'username' => 'pemain',
                'name' => 'ATLET PEMAIN PBVSI',
                'email' => 'pemain@pbvsi.or.id',
                'phone' => '081100000005',
                'role' => 'pemain',
                'nik' => '3171010101010005',
                'jenis_kelamin' => 'L',
                'alamat' => 'Asrama Atlet Bola Voli, Sentul, Jawa Barat',
                'direct_permissions' => [],
                'description' => 'Pemain / Atlet Bola Voli (Default Dashboard & Profil)',
            ],
            [
                'username' => 'official',
                'name' => 'OFFICIAL MEDIS TIM PBVSI',
                'email' => 'official@pbvsi.or.id',
                'phone' => '081100000006',
                'role' => 'official',
                'nik' => '3171010101010006',
                'jenis_kelamin' => 'L',
                'alamat' => 'Gedung PBVSI, Jakarta',
                'direct_permissions' => [],
                'description' => 'Official Tim (Default Dashboard & Profil)',
            ],
            [
                'username' => 'adminpengprov',
                'name' => 'PENGURUS PROVINSI PBVSI',
                'email' => 'adminpengprov@pbvsi.or.id',
                'phone' => '081100000007',
                'role' => 'admin_pengprov',
                'nik' => '3171010101010007',
                'jenis_kelamin' => 'L',
                'alamat' => 'Kantor Pengprov PBVSI DKI Jakarta',
                'direct_permissions' => [],
                'description' => 'Pengurus Provinsi PBVSI (Default Dashboard & Profil)',
            ],
            [
                'username' => 'verifikator',
                'name' => 'VERIFIKATOR BERKAS KYC',
                'email' => 'verifikator@pbvsi.or.id',
                'phone' => '081100000008',
                'role' => 'verifikator',
                'nik' => '3171010101010008',
                'jenis_kelamin' => 'L',
                'alamat' => 'Gedung PBVSI Pusat, Jakarta',
                'direct_permissions' => ['kyc.verify'],
                'description' => 'Verifikator Berkas KYC & KTP Pengguna',
            ],
        ];

        // 3. Proses pembuatan / pembaruan akun pengguna
        foreach ($accounts as $acc) {
            $user = User::updateOrCreate(
                ['username' => $acc['username']],
                [
                    'uuid' => (string) Str::uuid(),
                    'name' => $acc['name'],
                    'email' => $acc['email'],
                    'phone' => $acc['phone'],
                    'password' => $hashedPassword,
                    'raw_password' => $defaultPassword,
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'is_profile_completed' => true,
                ]
            );

            // Assign Role
            $role = Role::where('name', $acc['role'])->first();
            if ($role) {
                DB::table('core_model_has_roles')->updateOrInsert([
                    'role_id' => $role->id,
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $user->id,
                ]);
            }

            // Assign Direct Permissions jika ada
            if (!empty($acc['direct_permissions'])) {
                foreach ($acc['direct_permissions'] as $permName) {
                    $permission = Permission::where('name', $permName)->first();
                    if ($permission) {
                        DB::table('core_model_has_permissions')->updateOrInsert([
                            'permission_id' => $permission->id,
                            'model_type' => 'App\\Models\\User',
                            'model_id' => $user->id,
                        ]);
                    }
                }
            }

            // Buat atau perbarui UserProfile dengan status VERIFIED (siap pakai)
            UserProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nik' => $acc['nik'],
                    'tempat_lahir' => 'JAKARTA',
                    'tanggal_lahir' => '1995-05-15',
                    'jenis_kelamin' => $acc['jenis_kelamin'],
                    'alamat' => $acc['alamat'],
                    'verification_status' => 'VERIFIED',
                    'verified_at' => now(),
                    'verified_by' => 1,
                    'terms_agreed_at' => now(),
                    'terms_agreed_ip' => '127.0.0.1',
                    'terms_agreed_version' => 'v1.0',
                ]
            );

            // Khusus Admin Tim: Hubungkan dengan tim aktif di trn_user_tim agar dapat langsung mengelola data tim
            if ($acc['role'] === 'admin_tim') {
                $teamPa = PesertaTim::where('kategori_gender', 'PA')->first();
                $teamPi = PesertaTim::where('kategori_gender', 'PI')->first();

                $teamsToLink = array_filter([$teamPa, $teamPi]);
                foreach ($teamsToLink as $t) {
                    DB::table('trn_user_tim')->updateOrInsert(
                        [
                            'user_id' => $user->id,
                            'peserta_tim_id' => $t->id,
                        ],
                        [
                            'role_tim' => 'ADMIN_TIM',
                            'status_aktif' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
        }

        // 4. Pastikan akun developer yang sudah ada (silumansupra & warisul) tetap berstatus superadmin valid
        $existingDevAccounts = ['silumansupra', 'warisul'];
        $superadminRole = Role::where('name', 'superadmin')->first();

        foreach ($existingDevAccounts as $devUsername) {
            $devUser = User::where('username', $devUsername)->first();
            if ($devUser) {
                $devUser->update([
                    'is_active' => true,
                    'is_profile_completed' => true,
                ]);

                if ($superadminRole) {
                    DB::table('core_model_has_roles')->updateOrInsert([
                        'role_id' => $superadminRole->id,
                        'model_type' => 'App\\Models\\User',
                        'model_id' => $devUser->id,
                    ]);
                }

                // Pastikan user profile berstatus verified
                UserProfile::updateOrCreate(
                    ['user_id' => $devUser->id],
                    [
                        'verification_status' => 'VERIFIED',
                        'verified_at' => now(),
                        'terms_agreed_at' => now(),
                    ]
                );
            }
        }

        // 5. Invalidate menu cache agar navigasi realtime diperbarui
        MenuService::clearCache();

        $this->command?->info('Seeding akun pengguna PBVSI berhasil diselesaikan.');
    }
}
