<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Menu;
use App\Services\MenuService;

class DynamicMenuAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Permissions
        $permissions = [
            [
                'name' => 'kyc.verify',
                'display_name' => 'Verifikasi KYC & Dokumen KTP',
                'description' => 'Memeriksa berkas KTP & selfie KTP pengguna serta melakukan persetujuan/penolakan data.',
            ],
            [
                'name' => 'menu.manage',
                'display_name' => 'Manajemen Menu & Navigasi',
                'description' => 'Mengatur tata letak menu, submenu, ikon, status aktif/nonaktif, dan hak akses.',
            ],
            [
                'name' => 'user.manage',
                'display_name' => 'Manajemen Pengguna & RBAC',
                'description' => 'Mengelola akun pengguna, penetapan role, dan pemberian hak akses khusus/individual.',
            ],
            [
                'name' => 'master.data',
                'display_name' => 'Pengelolaan Master Data PBVSI',
                'description' => 'Mengelola data wilayah, pengprov, pengkot, klub/tim, jabatan, dan posisi pemain.',
            ],
            [
                'name' => 'personel.manage',
                'display_name' => 'Akses Bank Personel PBVSI',
                'description' => 'Mengelola database bank pemain, wasit, pelatih, dan official nasional.',
            ],
            [
                'name' => 'kejuaraan.manage',
                'display_name' => 'Manajemen Kejuaraan & Kompetisi',
                'description' => 'Mengelola kompetisi kelompok umur (U16-U19), Livoli, dan kejuaraan nasional.',
            ],
            [
                'name' => 'audit.view',
                'display_name' => 'Audit Log Sistem PBVSI',
                'description' => 'Melihat rekaman jejak audit aktivitas pengguna dan keamanan sistem.',
            ],
            [
                'name' => 'sertifikat.view',
                'display_name' => 'Akses Sertifikat & Lisensi PBVSI',
                'description' => 'Akses sertifikat lisensi kepelatihan, perwasitan, dan sertifikasi resmi PBVSI (dalam persiapan).',
            ],
            [
                'name' => 'tim.manage',
                'display_name' => 'Manajemen Data Tim & Roster O-2',
                'description' => 'Akses pendaftaran pemain, official, profil tim, dan berkas roster O-2 kontingen.',
            ],
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(
                ['name' => $perm['name']],
                [
                    'guard_name' => 'web',
                    'display_name' => $perm['display_name'],
                    'description' => $perm['description'],
                ]
            );
        }

        // Assign permission kyc.verify to role 'verifikator' if exists
        $verifikatorRole = Role::where('name', 'verifikator')->first();
        $kycPerm = Permission::where('name', 'kyc.verify')->first();
        if ($verifikatorRole && $kycPerm) {
            DB::table('core_role_has_permissions')->updateOrInsert([
                'permission_id' => $kycPerm->id,
                'role_id' => $verifikatorRole->id,
            ]);
        }

        // Assign sertifikat.view to wasit and pelatih roles
        $certPerm = Permission::where('name', 'sertifikat.view')->first();
        if ($certPerm) {
            foreach (['wasit', 'pelatih'] as $roleName) {
                $targetRole = Role::where('name', $roleName)->first();
                if ($targetRole) {
                    DB::table('core_role_has_permissions')->updateOrInsert([
                        'permission_id' => $certPerm->id,
                        'role_id' => $targetRole->id,
                    ]);
                }
            }
        }

        // Assign tim.manage to admin_tim role
        $timPerm = Permission::where('name', 'tim.manage')->first();
        $adminTimRole = Role::where('name', 'admin_tim')->first();
        if ($timPerm && $adminTimRole) {
            DB::table('core_role_has_permissions')->updateOrInsert([
                'permission_id' => $timPerm->id,
                'role_id' => $adminTimRole->id,
            ]);
        }

        // Assign all permissions to superadmin role
        $superadminRole = Role::where('name', 'superadmin')->first();
        if ($superadminRole) {
            $allPerms = Permission::all();
            foreach ($allPerms as $p) {
                DB::table('core_role_has_permissions')->updateOrInsert([
                    'permission_id' => $p->id,
                    'role_id' => $superadminRole->id,
                ]);
            }
        }

        // 2. Seed Default Core Menus
        $menus = [
            [
                'title' => 'Dashboard Utama',
                'route_name' => 'dashboard.index',
                'url' => null,
                'icon' => 'fa-solid fa-house',
                'permission_key' => null,
                'order' => 1,
                'is_active' => true,
                'badge_text' => null,
                'badge_class' => null,
                'children' => [],
            ],
            [
                'title' => 'Verifikasi KYC & KTP',
                'route_name' => 'master.kyc.index',
                'url' => null,
                'icon' => 'fa-solid fa-id-card-clip',
                'permission_key' => 'kyc.verify',
                'order' => 2,
                'is_active' => true,
                'badge_text' => 'KYC',
                'badge_class' => 'bg-danger',
                'children' => [],
            ],
            [
                'title' => 'Kejuaraan Kelompok Umur',
                'route_name' => null,
                'url' => '#!',
                'icon' => 'fa-solid fa-volleyball',
                'permission_key' => 'kejuaraan.manage',
                'order' => 3,
                'is_active' => true,
                'children' => [
                    [
                        'title' => 'Kejurnas U-16',
                        'route_name' => null,
                        'url' => '/kejuaraan/kejurnas-u16',
                        'icon' => 'fa-solid fa-circle-dot text-info',
                        'permission_key' => 'kejuaraan.manage',
                        'order' => 1,
                        'is_active' => true,
                    ],
                    [
                        'title' => 'Kejurnas U-17',
                        'route_name' => null,
                        'url' => '/kejuaraan/kejurnas-u17',
                        'icon' => 'fa-solid fa-circle-dot text-success',
                        'permission_key' => 'kejuaraan.manage',
                        'order' => 2,
                        'is_active' => true,
                    ],
                    [
                        'title' => 'Kejurnas U-18',
                        'route_name' => null,
                        'url' => '/kejuaraan/kejurnas-u18',
                        'icon' => 'fa-solid fa-circle-dot text-warning',
                        'permission_key' => 'kejuaraan.manage',
                        'order' => 3,
                        'is_active' => true,
                    ],
                    [
                        'title' => 'Kejurnas U-19',
                        'route_name' => null,
                        'url' => '/kejuaraan/kejurnas-u19',
                        'icon' => 'fa-solid fa-circle-dot text-danger',
                        'permission_key' => 'kejuaraan.manage',
                        'order' => 4,
                        'is_active' => true,
                    ],
                ],
            ],
            [
                'title' => 'Liga Voli (Livoli)',
                'route_name' => null,
                'url' => '#!',
                'icon' => 'fa-solid fa-trophy',
                'permission_key' => 'kejuaraan.manage',
                'order' => 4,
                'is_active' => true,
                'children' => [
                    [
                        'title' => 'Livoli Divisi Utama',
                        'route_name' => null,
                        'url' => '/kejuaraan/livoli-divisi-utama',
                        'icon' => 'fa-solid fa-trophy text-warning',
                        'permission_key' => 'kejuaraan.manage',
                        'order' => 1,
                        'is_active' => true,
                    ],
                    [
                        'title' => 'Livoli Divisi I',
                        'route_name' => null,
                        'url' => '/kejuaraan/livoli-divisi-1',
                        'icon' => 'fa-solid fa-medal text-primary',
                        'permission_key' => 'kejuaraan.manage',
                        'order' => 2,
                        'is_active' => true,
                    ],
                ],
            ],
            [
                'title' => 'Database Personel PBVSI',
                'route_name' => null,
                'url' => '#!',
                'icon' => 'fa-solid fa-users',
                'permission_key' => 'personel.manage',
                'order' => 5,
                'is_active' => true,
                'children' => [
                    [
                        'title' => 'Bank Pemain',
                        'route_name' => 'personel.pemain',
                        'url' => null,
                        'icon' => 'fa-solid fa-id-card-clip text-primary',
                        'permission_key' => 'personel.manage',
                        'order' => 1,
                        'is_active' => true,
                    ],
                    [
                        'title' => 'Data Pelatih',
                        'route_name' => 'personel.pelatih',
                        'url' => null,
                        'icon' => 'fa-solid fa-chalkboard-user text-warning',
                        'permission_key' => 'personel.manage',
                        'order' => 2,
                        'is_active' => true,
                    ],
                    [
                        'title' => 'Data Wasit',
                        'route_name' => 'personel.wasit',
                        'url' => null,
                        'icon' => 'fa-solid fa-flag text-danger',
                        'permission_key' => 'personel.manage',
                        'order' => 3,
                        'is_active' => true,
                    ],
                    [
                        'title' => 'Official Tim',
                        'route_name' => 'personel.official',
                        'url' => null,
                        'icon' => 'fa-solid fa-user-tie text-info',
                        'permission_key' => 'personel.manage',
                        'order' => 4,
                        'is_active' => true,
                    ],
                ],
            ],
            [
                'title' => 'Pengaturan & Master Data',
                'route_name' => null,
                'url' => '#!',
                'icon' => 'fa-solid fa-sliders',
                'permission_key' => 'master.data',
                'order' => 6,
                'is_active' => true,
                'children' => [
                    [
                        'title' => 'Master Wilayah',
                        'route_name' => 'master.wilayah',
                        'url' => null,
                        'icon' => 'fa-solid fa-map-location-dot',
                        'permission_key' => 'master.data',
                        'order' => 1,
                        'is_active' => true,
                    ],
                    [
                        'title' => 'Data Pengprov',
                        'route_name' => 'master.pengprov',
                        'url' => null,
                        'icon' => 'fa-solid fa-building-flag',
                        'permission_key' => 'master.data',
                        'order' => 2,
                        'is_active' => true,
                    ],
                    [
                        'title' => 'Data Pengkot/Pengkab',
                        'route_name' => 'master.pengkot',
                        'url' => null,
                        'icon' => 'fa-solid fa-city',
                        'permission_key' => 'master.data',
                        'order' => 3,
                        'is_active' => true,
                    ],
                    [
                        'title' => 'Data Tim / Klub',
                        'route_name' => 'master.klub',
                        'url' => null,
                        'icon' => 'fa-solid fa-shield-halved',
                        'permission_key' => 'master.data',
                        'order' => 4,
                        'is_active' => true,
                    ],
                    [
                        'title' => 'Admin Tim / Kontingen',
                        'route_name' => 'master.admin_tim',
                        'url' => null,
                        'icon' => 'fa-solid fa-id-badge text-warning',
                        'permission_key' => 'master.data',
                        'order' => 5,
                        'is_active' => true,
                    ],
                    [
                        'title' => 'Master Jabatan',
                        'route_name' => 'master.jabatan',
                        'url' => null,
                        'icon' => 'fa-solid fa-user-tag',
                        'permission_key' => 'master.data',
                        'order' => 6,
                        'is_active' => true,
                    ],
                    [
                        'title' => 'Posisi Pemain',
                        'route_name' => 'master.posisi',
                        'url' => null,
                        'icon' => 'fa-solid fa-arrows-down-to-people',
                        'permission_key' => 'master.data',
                        'order' => 7,
                        'is_active' => true,
                    ],
                ],
            ],
            [
                'title' => 'Sistem & Akses Kontrol',
                'route_name' => null,
                'url' => '#!',
                'icon' => 'fa-solid fa-shield-halved',
                'permission_key' => 'user.manage',
                'order' => 7,
                'is_active' => true,
                'children' => [
                    [
                        'title' => 'Manajemen Menu',
                        'route_name' => 'master.menus.index',
                        'url' => null,
                        'icon' => 'fa-solid fa-bars-staggered text-primary',
                        'permission_key' => 'menu.manage',
                        'order' => 1,
                        'is_active' => true,
                    ],
                    [
                        'title' => 'User & Akses RBAC',
                        'route_name' => 'master.users',
                        'url' => null,
                        'icon' => 'fa-solid fa-user-shield text-success',
                        'permission_key' => 'user.manage',
                        'order' => 2,
                        'is_active' => true,
                    ],
                    [
                        'title' => 'Audit Log Sistem',
                        'route_name' => 'audit-logs.index',
                        'url' => null,
                        'icon' => 'fa-solid fa-clock-rotate-left text-danger',
                        'permission_key' => 'audit.view',
                        'order' => 3,
                        'is_active' => true,
                    ],
                ],
            ],
        ];

        foreach ($menus as $m) {
            $children = $m['children'] ?? [];
            unset($m['children']);

            $rootMenu = Menu::updateOrCreate(
                [
                    'parent_id' => null,
                    'title' => $m['title'],
                ],
                $m
            );

            foreach ($children as $c) {
                $c['parent_id'] = $rootMenu->id;
                Menu::updateOrCreate(
                    [
                        'parent_id' => $rootMenu->id,
                        'title' => $c['title'],
                    ],
                    $c
                );
            }
        }

        // Invalidate menu cache
        MenuService::clearCache();
    }
}
