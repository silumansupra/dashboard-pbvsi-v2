# Spesifikasi Skema Database Modul e-Score PBVSI

Dokumen ini memuat DDL migration Laravel lengkap untuk tabel-tabel modul e-Score.

---

## Migration: `create_escore_match_tables.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Jadwal Pertandingan
        Schema::create('trn_jadwal_pertandingan', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('edisi_id');
            $table->string('nomor_pertandingan', 20); // Contoh: "Match 01", "PA-05"
            $table->string('fase_grup', 50)->default('Penyisihan Pool'); // Babak 8 Besar, Semifinal, Final
            $table->string('pool', 10)->nullable(); // Pool A, Pool B
            $table->unsignedInteger('tim_a_id');
            $table->unsignedInteger('tim_b_id');
            $table->dateTime('jadwal_mulai');
            $table->string('lapangan_hall', 100)->nullable();
            
            // Wasit & Komite Pertandingan
            $table->unsignedBigInteger('wasit_r1_id')->nullable();
            $table->unsignedBigInteger('wasit_r2_id')->nullable();
            $table->unsignedBigInteger('wasit_rc_id')->nullable(); // Challenge Referee
            $table->unsignedBigInteger('scorer_id')->nullable();
            $table->unsignedBigInteger('assistant_scorer_id')->nullable();

            $table->enum('status', ['SCHEDULED', 'WARMUP', 'LIVE', 'SET_BREAK', 'FINISHED', 'POSTPONED'])->default('SCHEDULED');
            $table->timestamps();

            $table->foreign('edisi_id')->references('id')->on('trn_edisi')->onDelete('cascade');
            $table->foreign('tim_a_id')->references('id')->on('trn_peserta_tim')->onDelete('cascade');
            $table->foreign('tim_b_id')->references('id')->on('trn_peserta_tim')->onDelete('cascade');
            $table->foreign('wasit_r1_id')->references('id')->on('mbr_wasit')->onDelete('set null');
            $table->foreign('wasit_r2_id')->references('id')->on('mbr_wasit')->onDelete('set null');
        });

        // 2. Sesi Pertandingan eScore (Kondisi Lapangan, Tanda Tangan, Live State)
        Schema::create('trn_escore_session', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('jadwal_id')->unique();
            $table->integer('set_aktif')->default(1);
            $table->integer('skor_set_a')->default(0);
            $table->integer('skor_set_b')->default(0);
            $table->string('warna_jersey_a', 30)->nullable();
            $table->string('warna_jersey_b', 30)->nullable();
            $table->string('suhu_ruangan', 20)->nullable();
            $table->string('kelembaban', 20)->nullable();
            $table->integer('penonton')->default(0);
            
            // Tanda Tangan Digital Pra & Pasca Pertandingan (Base64 PNG dari Canvas)
            $table->longText('sign_kapten_a')->nullable();
            $table->longText('sign_kapten_b')->nullable();
            $table->longText('sign_coach_a')->nullable();
            $table->longText('sign_coach_b')->nullable();
            $table->longText('sign_r1')->nullable();
            $table->longText('sign_r2')->nullable();
            $table->longText('sign_scorer')->nullable();
            $table->longText('sign_post_kapten_a')->nullable();
            $table->longText('sign_post_kapten_b')->nullable();

            $table->timestamps();
            $table->foreign('jadwal_id')->references('id')->on('trn_jadwal_pertandingan')->onDelete('cascade');
        });

        // 3. Rekapitulasi Data Per Set
        Schema::create('trn_escore_set', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('session_id');
            $table->integer('nomor_set'); // 1, 2, 3, 4, 5
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->integer('durasi_menit')->default(0);
            $table->enum('tim_servis_awal', ['A', 'B'])->default('A');
            $table->enum('tim_sisi_kiri', ['A', 'B'])->default('A');
            
            // Starting Lineup I s/d VI (JSON array ID Roster trn_roster)
            $table->json('starting_lineup_a'); // [pos1, pos2, pos3, pos4, pos5, pos6]
            $table->json('starting_lineup_b');
            $table->json('liberos_a')->nullable();
            $table->json('liberos_b')->nullable();

            $table->integer('poin_a')->default(0);
            $table->integer('poin_b')->default(0);
            $table->integer('timeout_a')->default(0);
            $table->integer('timeout_b')->default(0);
            $table->integer('substitusi_a')->default(0);
            $table->integer('substitusi_b')->default(0);
            $table->enum('pemenang', ['A', 'B'])->nullable();
            $table->timestamps();

            $table->foreign('session_id')->references('id')->on('trn_escore_session')->onDelete('cascade');
            $table->unique(['session_id', 'nomor_set']);
        });

        // 4. Audit Log Detail Reli (Poin, Rotasi, Substitusi, Libero, Challenge, Sanksi)
        Schema::create('trn_escore_action_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('set_id');
            $table->integer('nomor_reli');
            $table->enum('tipe_aksi', [
                'POINT', 'SUBSTITUTION', 'LIBERO_REPLACE', 
                'TIMEOUT', 'CHALLENGE', 'SANCTION', 'CHANGE_SIDE', 'UNDO'
            ]);
            $table->enum('tim', ['A', 'B']);
            $table->unsignedBigInteger('pemain_id')->nullable();
            $table->unsignedBigInteger('pemain_keluar_id')->nullable();
            $table->integer('skor_a_sebelum');
            $table->integer('skor_b_sebelum');
            $table->integer('skor_a_sesudah');
            $table->integer('skor_b_sesudah');
            $table->json('posisi_court_a'); // Snapshot 6 pemain di lapangan saat reli ini
            $table->json('posisi_court_b');
            $table->json('meta_data')->nullable(); // detail kartu sanksi (W/P/E/D), tipe challenge
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('set_id')->references('id')->on('trn_escore_set')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trn_escore_action_log');
        Schema::dropIfExists('trn_escore_set');
        Schema::dropIfExists('trn_escore_session');
        Schema::dropIfExists('trn_jadwal_pertandingan');
    }
};
```
