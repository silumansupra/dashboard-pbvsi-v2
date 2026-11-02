<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Turnamen;
use App\Models\Edisi;

class SeedTournamentEditions extends Command
{
    protected $signature = 'pbvsi:seed-editions';
    protected $description = 'Seed all tournament editions for Livoli, Kapolri Cup, Voli Pantai, and past years';

    public function handle()
    {
        $this->info("=== Seeding Turnamen Editions ===");

        $editions = [
            // 1. Kejurnas U-16 (turnamen_id = 1)
            [
                'turnamen_id' => 1,
                'tahun' => 2025,
                'slug_edisi' => 'kejurnas-u16-2025',
                'nama_edisi' => 'Kejuaraan Nasional Bola Voli Antar Klub U-16 Tahun 2025 Sentul',
                'kota_tuan_rumah' => 'Bogor',
                'venue_utama' => 'Padepokan Voli Jenderal Polisi Kunarto, Sentul',
                'min_tanggal_lahir' => '2009-01-01',
                'max_tanggal_lahir' => '2010-12-31',
                'status_edisi' => 'FINISHED',
            ],
            // 2. Kejurnas U-17 (turnamen_id = 2)
            [
                'turnamen_id' => 2,
                'tahun' => 2025,
                'slug_edisi' => 'kejurnas-u17-2025',
                'nama_edisi' => 'Kejuaraan Nasional Bola Voli Antar Klub U-17 Tahun 2025 Surakarta',
                'kota_tuan_rumah' => 'Surakarta',
                'venue_utama' => 'GOR Sritex Arena',
                'min_tanggal_lahir' => '2008-01-01',
                'max_tanggal_lahir' => '2009-12-31',
                'status_edisi' => 'FINISHED',
            ],
            [
                'turnamen_id' => 2,
                'tahun' => 2024,
                'slug_edisi' => 'kejurnas-u17-2024',
                'nama_edisi' => 'Kejuaraan Nasional Bola Voli Antar Klub U-17 Tahun 2024 Yogyakarta',
                'kota_tuan_rumah' => 'Yogyakarta',
                'venue_utama' => 'GOR Pangukan Sleman',
                'min_tanggal_lahir' => '2007-01-01',
                'max_tanggal_lahir' => '2008-12-31',
                'status_edisi' => 'FINISHED',
            ],
            // 3. Kejurnas U-18 (turnamen_id = 3)
            [
                'turnamen_id' => 3,
                'tahun' => 2025,
                'slug_edisi' => 'kejurnas-u18-2025',
                'nama_edisi' => 'Kejuaraan Nasional Bola Voli Antar Klub U-18 Tahun 2025 Sidoarjo',
                'kota_tuan_rumah' => 'Sidoarjo',
                'venue_utama' => 'GOR Delta Sidoarjo',
                'min_tanggal_lahir' => '2007-01-01',
                'max_tanggal_lahir' => '2008-12-31',
                'status_edisi' => 'FINISHED',
            ],
            // 4. Kejurnas U-19 (turnamen_id = 4)
            [
                'turnamen_id' => 4,
                'tahun' => 2024,
                'slug_edisi' => 'kejurnas-u19-2024',
                'nama_edisi' => 'Kejuaraan Nasional Bola Voli Junior U-19 Tahun 2024 Tangerang',
                'kota_tuan_rumah' => 'Tangerang',
                'venue_utama' => 'GOR Gondrong',
                'min_tanggal_lahir' => '2005-01-01',
                'max_tanggal_lahir' => '2007-12-31',
                'status_edisi' => 'FINISHED',
            ],
            // 5. Livoli Divisi 1 (turnamen_id = 5)
            [
                'turnamen_id' => 5,
                'tahun' => 2026,
                'slug_edisi' => 'livoli-divisi-1-2026',
                'nama_edisi' => 'Liga Bola Voli Indonesia (Livoli) Divisi I Tahun 2026',
                'kota_tuan_rumah' => 'Grobogan',
                'venue_utama' => 'GOR Bung Karno Simpang Lima',
                'min_tanggal_lahir' => null,
                'max_tanggal_lahir' => null,
                'status_edisi' => 'REGISTRASI',
            ],
            [
                'turnamen_id' => 5,
                'tahun' => 2025,
                'slug_edisi' => 'livoli-divisi-1-2025',
                'nama_edisi' => 'Liga Bola Voli Indonesia (Livoli) Divisi I Tahun 2025',
                'kota_tuan_rumah' => 'Lombok Tengah',
                'venue_utama' => 'GOR Poltekpar Lombok',
                'min_tanggal_lahir' => null,
                'max_tanggal_lahir' => null,
                'status_edisi' => 'FINISHED',
            ],
            [
                'turnamen_id' => 5,
                'tahun' => 2024,
                'slug_edisi' => 'livoli-divisi-1-2024',
                'nama_edisi' => 'Liga Bola Voli Indonesia (Livoli) Divisi I Tahun 2024',
                'kota_tuan_rumah' => 'Tuban',
                'venue_utama' => 'GOR Rangga Jaya Anoraga',
                'min_tanggal_lahir' => null,
                'max_tanggal_lahir' => null,
                'status_edisi' => 'FINISHED',
            ],
            // 6. Livoli Divisi Utama (turnamen_id = 6)
            [
                'turnamen_id' => 6,
                'tahun' => 2026,
                'slug_edisi' => 'livoli-divisi-utama-2026',
                'nama_edisi' => 'Liga Bola Voli Indonesia (Livoli) Divisi Utama Tahun 2026',
                'kota_tuan_rumah' => 'Magetan & Surabaya',
                'venue_utama' => 'GOR Ki Mageti & DBL Arena',
                'min_tanggal_lahir' => null,
                'max_tanggal_lahir' => null,
                'status_edisi' => 'REGISTRASI',
            ],
            [
                'turnamen_id' => 6,
                'tahun' => 2025,
                'slug_edisi' => 'livoli-divisi-utama-2025',
                'nama_edisi' => 'Liga Bola Voli Indonesia (Livoli) Divisi Utama Tahun 2025',
                'kota_tuan_rumah' => 'Bogor & Gresik',
                'venue_utama' => 'GOR Laga Tangkas & Tri Dharma',
                'min_tanggal_lahir' => null,
                'max_tanggal_lahir' => null,
                'status_edisi' => 'FINISHED',
            ],
            [
                'turnamen_id' => 6,
                'tahun' => 2024,
                'slug_edisi' => 'livoli-divisi-utama-2024',
                'nama_edisi' => 'Liga Bola Voli Indonesia (Livoli) Divisi Utama Tahun 2024',
                'kota_tuan_rumah' => 'Banyuwangi & Gresik',
                'venue_utama' => 'GOR Tawangalun & Tri Dharma',
                'min_tanggal_lahir' => null,
                'max_tanggal_lahir' => null,
                'status_edisi' => 'FINISHED',
            ],
            // 7. Sirkuit Nasional Voli Pantai (turnamen_id = 7)
            [
                'turnamen_id' => 7,
                'tahun' => 2026,
                'slug_edisi' => 'sirkuit-nasional-voli-pantai-2026',
                'nama_edisi' => 'Sirkuit Nasional Bola Voli Pantai PBVSI Seri 2026',
                'kota_tuan_rumah' => 'Pantai Parangtritis, DI Yogyakarta',
                'venue_utama' => 'Arena Voli Pantai Parangtritis',
                'min_tanggal_lahir' => null,
                'max_tanggal_lahir' => null,
                'status_edisi' => 'REGISTRASI',
            ],
            [
                'turnamen_id' => 7,
                'tahun' => 2025,
                'slug_edisi' => 'sirkuit-nasional-voli-pantai-2025',
                'nama_edisi' => 'Sirkuit Nasional Bola Voli Pantai PBVSI Seri 2025',
                'kota_tuan_rumah' => 'Pantai Kuta, Bali',
                'venue_utama' => 'Kuta Beach Volleyball Arena',
                'min_tanggal_lahir' => null,
                'max_tanggal_lahir' => null,
                'status_edisi' => 'FINISHED',
            ],
            // 8. Kapolri Cup (turnamen_id = 8)
            [
                'turnamen_id' => 8,
                'tahun' => 2026,
                'slug_edisi' => 'kapolri-cup-2026',
                'nama_edisi' => 'Kejuaraan Bola Voli Piala Kapolri (Kapolri Cup) 2026',
                'kota_tuan_rumah' => 'Pontianak & Jakarta',
                'venue_utama' => 'GOR Terpadu Ayani Pontianak',
                'min_tanggal_lahir' => null,
                'max_tanggal_lahir' => null,
                'status_edisi' => 'REGISTRASI',
            ],
            [
                'turnamen_id' => 8,
                'tahun' => 2025,
                'slug_edisi' => 'kapolri-cup-2025',
                'nama_edisi' => 'Kejuaraan Bola Voli Piala Kapolri (Kapolri Cup) 2025',
                'kota_tuan_rumah' => 'Pontianak, Kalimantan Barat',
                'venue_utama' => 'GOR Pangsuma Pontianak',
                'min_tanggal_lahir' => null,
                'max_tanggal_lahir' => null,
                'status_edisi' => 'FINISHED',
            ],
            [
                'turnamen_id' => 8,
                'tahun' => 2023,
                'slug_edisi' => 'kapolri-cup-2023',
                'nama_edisi' => 'Kejuaraan Bola Voli Piala Kapolri (Kapolri Cup) 2023',
                'kota_tuan_rumah' => 'Pontianak, Kalimantan Barat',
                'venue_utama' => 'GOR Pangsuma Pontianak',
                'min_tanggal_lahir' => null,
                'max_tanggal_lahir' => null,
                'status_edisi' => 'FINISHED',
            ],
        ];

        foreach ($editions as $item) {
            $exists = DB::table('trn_edisi')->where('slug_edisi', $item['slug_edisi'])->exists();
            if (!$exists) {
                DB::table('trn_edisi')->insert(array_merge($item, [
                    'max_kuota_pemain' => 14,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
                $this->info("Inserted: {$item['slug_edisi']}");
            }
        }

        $this->info("Total edisi now: " . DB::table('trn_edisi')->count());
    }
}
