<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterJabatanAndPosisiSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Posisi Pemain
        $posisi = [
            ['id' => 1, 'nama_posisi' => 'LIBERO', 'kode_posisi' => 'L', 'deskripsi' => 'Pemain bertahan spesialis penerima bola dan bertahan'],
            ['id' => 2, 'nama_posisi' => 'MIDDLE BLOCKER', 'kode_posisi' => 'MB', 'deskripsi' => 'Spesialis blok tengah dan quick spike'],
            ['id' => 3, 'nama_posisi' => 'OUTSIDE HITTER', 'kode_posisi' => 'OH', 'deskripsi' => 'Penyerang sayap kiri dan penerima servis'],
            ['id' => 4, 'nama_posisi' => 'OPPOSITE HITTER', 'kode_posisi' => 'OP', 'deskripsi' => 'Penyerang utama posisi silang setter'],
            ['id' => 5, 'nama_posisi' => 'SETTER', 'kode_posisi' => 'S', 'deskripsi' => 'Pengatur ritme serangan dan pemberi umpan'],
        ];

        foreach ($posisi as $pos) {
            DB::table('core_master_posisi_pemain')->updateOrInsert(
                ['kode_posisi' => $pos['kode_posisi']],
                array_merge($pos, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }

        // 2. Master Jabatan
        $jabatan = [
            ['id' => 1, 'nama_jabatan' => 'Web Administrator', 'is_official_tim' => false],
            ['id' => 2, 'nama_jabatan' => 'Direksi', 'is_official_tim' => false],
            ['id' => 3, 'nama_jabatan' => 'Manajer Tim', 'is_official_tim' => true],
            ['id' => 4, 'nama_jabatan' => 'Head Coach', 'is_official_tim' => true],
            ['id' => 5, 'nama_jabatan' => 'Assistant Coach 1', 'is_official_tim' => true],
            ['id' => 6, 'nama_jabatan' => 'Assistant Coach 2', 'is_official_tim' => true],
            ['id' => 7, 'nama_jabatan' => 'Ast. Team Manager 1', 'is_official_tim' => true],
            ['id' => 8, 'nama_jabatan' => 'Ast. Team Manager 2', 'is_official_tim' => true],
            ['id' => 9, 'nama_jabatan' => 'Translator', 'is_official_tim' => true],
            ['id' => 10, 'nama_jabatan' => 'Therapist / Fisioterapi', 'is_official_tim' => true],
            ['id' => 11, 'nama_jabatan' => 'Dokter / Medis', 'is_official_tim' => true],
            ['id' => 12, 'nama_jabatan' => 'Trainer Fisik', 'is_official_tim' => true],
            ['id' => 13, 'nama_jabatan' => 'Wasit', 'is_official_tim' => false],
            ['id' => 14, 'nama_jabatan' => 'Pengurus Provinsi', 'is_official_tim' => false],
            ['id' => 15, 'nama_jabatan' => 'Pengurus Kota/Kab', 'is_official_tim' => false],
            ['id' => 16, 'nama_jabatan' => 'Pengurus Klub', 'is_official_tim' => false],
            ['id' => 17, 'nama_jabatan' => 'Ketua Umum', 'is_official_tim' => false],
            ['id' => 18, 'nama_jabatan' => 'Sekretaris Umum', 'is_official_tim' => false],
            ['id' => 19, 'nama_jabatan' => 'Bendahara', 'is_official_tim' => false],
            ['id' => 20, 'nama_jabatan' => 'Pemilik Klub', 'is_official_tim' => false],
        ];

        foreach ($jabatan as $j) {
            DB::table('core_master_jabatan')->updateOrInsert(
                ['id' => $j['id']],
                array_merge($j, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
