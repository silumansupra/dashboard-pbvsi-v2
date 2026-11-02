<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterTurnamenSeeder extends Seeder
{
    public function run(): void
    {
        $turnamen = [
            [
                'slug' => 'kejurnas-u16',
                'nama_turnamen' => 'Kejuaraan Nasional Bola Voli Antar Klub U-16',
                'kategori' => 'KEJURNAS_UMUR',
                'kelompok_umur' => 'U16',
                'jenis_lapangan' => 'INDOOR',
                'deskripsi' => 'Kejurnas kelompok umur U-16 binaan klub seluruh Indonesia'
            ],
            [
                'slug' => 'kejurnas-u17',
                'nama_turnamen' => 'Kejuaraan Nasional Bola Voli Antar Klub U-17',
                'kategori' => 'KEJURNAS_UMUR',
                'kelompok_umur' => 'U17',
                'jenis_lapangan' => 'INDOOR',
                'deskripsi' => 'Kejurnas kelompok umur U-17 binaan klub seluruh Indonesia'
            ],
            [
                'slug' => 'kejurnas-u18',
                'nama_turnamen' => 'Kejuaraan Nasional Bola Voli Antar Klub U-18',
                'kategori' => 'KEJURNAS_UMUR',
                'kelompok_umur' => 'U18',
                'jenis_lapangan' => 'INDOOR',
                'deskripsi' => 'Kejurnas kelompok umur U-18 antar klub se-Indonesia'
            ],
            [
                'slug' => 'kejurnas-u19',
                'nama_turnamen' => 'Kejuaraan Nasional Bola Voli Antar Klub U-19',
                'kategori' => 'KEJURNAS_UMUR',
                'kelompok_umur' => 'U19',
                'jenis_lapangan' => 'INDOOR',
                'deskripsi' => 'Kejurnas kelompok umur U-19 (junior) antar klub se-Indonesia'
            ],
            [
                'slug' => 'livoli-divisi-1',
                'nama_turnamen' => 'Liga Bola Voli Indonesia (Livoli) Divisi I',
                'kategori' => 'LIVOLI',
                'kelompok_umur' => 'SENIOR',
                'jenis_lapangan' => 'INDOOR',
                'deskripsi' => 'Kompetisi liga voli amatir kasta kedua PBVSI'
            ],
            [
                'slug' => 'livoli-divisi-utama',
                'nama_turnamen' => 'Liga Bola Voli Indonesia (Livoli) Divisi Utama',
                'kategori' => 'LIVOLI',
                'kelompok_umur' => 'SENIOR',
                'jenis_lapangan' => 'INDOOR',
                'deskripsi' => 'Kompetisi liga voli amatir kasta tertinggi PBVSI'
            ],
            [
                'slug' => 'sirkuit-nasional-voli-pantai',
                'nama_turnamen' => 'Sirkuit Nasional Bola Voli Pantai PBVSI',
                'kategori' => 'VOLI_PANTAI',
                'kelompok_umur' => 'OPEN',
                'jenis_lapangan' => 'BEACH',
                'deskripsi' => 'Sirkuit nasional bola voli pantai putra & putri'
            ],
            [
                'slug' => 'kapolri-cup',
                'nama_turnamen' => 'Kejuaraan Bola Voli Kapolri Cup',
                'kategori' => 'KAPOLRI_CUP',
                'kelompok_umur' => 'OPEN',
                'jenis_lapangan' => 'INDOOR',
                'deskripsi' => 'Turnamen bola voli piala Kapolri zona daerah & putaran nasional'
            ]
        ];

        foreach ($turnamen as $t) {
            DB::table('trn_master_turnamen')->updateOrInsert(
                ['slug' => $t['slug']],
                array_merge($t, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // Default Edisi Berjalan
        $edisi = [
            [
                'turnamen_slug' => 'kejurnas-u18',
                'tahun' => 2026,
                'slug_edisi' => 'kejurnas-u18-2026',
                'nama_edisi' => 'Kejuaraan Nasional Bola Voli U-18 Tahun 2026 DKI Jakarta',
                'kota_tuan_rumah' => 'DKI Jakarta',
                'venue_utama' => 'GOR Bulungan',
                'tanggal_mulai' => '2026-06-01',
                'tanggal_selesai' => '2026-06-10',
                'min_tanggal_lahir' => '2008-01-01',
                'max_tanggal_lahir' => '2009-12-31',
                'status_edisi' => 'REGISTRASI'
            ],
            [
                'turnamen_slug' => 'kejurnas-u19',
                'tahun' => 2025,
                'slug_edisi' => 'kejurnas-u19-2025',
                'nama_edisi' => 'Kejuaraan Nasional Bola Voli Junior U-19 Tahun 2025 Probolinggo',
                'kota_tuan_rumah' => 'Probolinggo',
                'venue_utama' => 'GOR Kedopok',
                'tanggal_mulai' => '2025-11-17',
                'tanggal_selesai' => '2025-11-24',
                'min_tanggal_lahir' => '2006-01-01',
                'max_tanggal_lahir' => '2008-12-31',
                'status_edisi' => 'FINISHED'
            ],
            [
                'turnamen_slug' => 'kejurnas-u19',
                'tahun' => 2026,
                'slug_edisi' => 'kejurnas-u19-2026',
                'nama_edisi' => 'Kejuaraan Nasional Bola Voli Junior U-19 Tahun 2026',
                'kota_tuan_rumah' => 'TBA',
                'venue_utama' => 'TBA',
                'tanggal_mulai' => '2026-11-01',
                'tanggal_selesai' => '2026-11-10',
                'min_tanggal_lahir' => '2007-01-01',
                'max_tanggal_lahir' => '2009-12-31',
                'status_edisi' => 'REGISTRASI'
            ],
            [
                'turnamen_slug' => 'kejurnas-u17',
                'tahun' => 2026,
                'slug_edisi' => 'kejurnas-u17-2026',
                'nama_edisi' => 'Kejuaraan Nasional Bola Voli Antar Klub U-17 Tahun 2026',
                'kota_tuan_rumah' => 'Yogyakarta',
                'venue_utama' => 'GOR Amongrogo',
                'tanggal_mulai' => '2026-07-15',
                'tanggal_selesai' => '2026-07-22',
                'min_tanggal_lahir' => '2009-01-01',
                'max_tanggal_lahir' => '2010-12-31',
                'status_edisi' => 'REGISTRASI'
            ],
            [
                'turnamen_slug' => 'kejurnas-u16',
                'tahun' => 2026,
                'slug_edisi' => 'kejurnas-u16-2026',
                'nama_edisi' => 'Kejuaraan Nasional Bola Voli Antar Klub U-16 Tahun 2026',
                'kota_tuan_rumah' => 'Bandung',
                'venue_utama' => 'GOR Saparua',
                'tanggal_mulai' => '2026-08-10',
                'tanggal_selesai' => '2026-08-17',
                'min_tanggal_lahir' => '2010-01-01',
                'max_tanggal_lahir' => '2011-12-31',
                'status_edisi' => 'REGISTRASI'
            ]
        ];

        foreach ($edisi as $ed) {
            $tId = DB::table('trn_master_turnamen')->where('slug', $ed['turnamen_slug'])->value('id');
            if ($tId) {
                unset($ed['turnamen_slug']);
                $ed['turnamen_id'] = $tId;
                DB::table('trn_edisi')->updateOrInsert(
                    ['slug_edisi' => $ed['slug_edisi']],
                    array_merge($ed, ['created_at' => now(), 'updated_at' => now()])
                );
            }
        }
    }
}
