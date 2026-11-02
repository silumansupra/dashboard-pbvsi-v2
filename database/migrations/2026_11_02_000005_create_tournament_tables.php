<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Master Turnamen
        Schema::create('trn_master_turnamen', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug', 100)->unique();
            $table->string('nama_turnamen', 150);
            $table->enum('kategori', ['KEJURNAS_UMUR', 'LIVOLI', 'VOLI_PANTAI', 'KAPOLRI_CUP', 'OPEN'])->default('KEJURNAS_UMUR');
            $table->enum('kelompok_umur', ['U16', 'U17', 'U18', 'U19', 'SENIOR', 'OPEN'])->default('OPEN');
            $table->enum('jenis_lapangan', ['INDOOR', 'BEACH'])->default('INDOOR');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Edisi Turnamen Per Tahun
        Schema::create('trn_edisi', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('turnamen_id');
            $table->integer('tahun');
            $table->string('slug_edisi', 100)->unique();
            $table->string('nama_edisi', 200);
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('kota_tuan_rumah', 100)->nullable();
            $table->string('venue_utama', 150)->nullable();
            $table->date('min_tanggal_lahir')->nullable();
            $table->date('max_tanggal_lahir')->nullable();
            $table->integer('max_kuota_pemain')->default(14);
            $table->enum('status_edisi', ['DRAFT', 'REGISTRASI', 'RUNNING', 'FINISHED'])->default('DRAFT');
            $table->timestamps();

            $table->foreign('turnamen_id')->references('id')->on('trn_master_turnamen')->onDelete('cascade');
            $table->index(['turnamen_id', 'tahun']);
        });

        // 3. Peserta Tim Turnamen Edisi
        Schema::create('trn_peserta_tim', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('edisi_id');
            $table->unsignedInteger('klub_id')->nullable();
            $table->unsignedInteger('pengprov_id')->nullable();
            $table->string('nama_tim', 150);
            $table->string('kode_tim', 50)->nullable();
            $table->enum('kategori_gender', ['PA', 'PI']);
            $table->string('pool_grup', 10)->nullable();
            $table->string('warna_jersey_1', 50)->nullable();
            $table->string('warna_jersey_2', 50)->nullable();
            $table->string('warna_jersey_3', 50)->nullable();
            $table->string('warna_jersey_libero_1', 50)->nullable();
            $table->string('warna_jersey_libero_2', 50)->nullable();
            $table->string('file_jersey_1', 255)->nullable();
            $table->string('file_jersey_2', 255)->nullable();
            $table->string('file_jersey_3', 255)->nullable();
            $table->string('logo_tim_path', 255)->nullable();
            $table->string('foto_skuad_path', 255)->nullable();
            $table->enum('status_verifikasi', ['DRAFT', 'SUBMITTED', 'VERIFIED', 'REJECTED'])->default('DRAFT');
            $table->text('catatan_verifikasi')->nullable();
            $table->timestamps();

            $table->foreign('edisi_id')->references('id')->on('trn_edisi')->onDelete('cascade');
            $table->foreign('klub_id')->references('id')->on('org_klub')->onDelete('set null');
            $table->foreign('pengprov_id')->references('id')->on('org_pengprov')->onDelete('set null');
            $table->index(['edisi_id', 'kategori_gender']);
        });

        // 4. Roster Pemain Tim Turnamen
        Schema::create('trn_roster', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('peserta_tim_id');
            $table->unsignedBigInteger('pemain_id');
            $table->integer('nomor_punggung');
            $table->string('nama_jersey', 100)->nullable();
            $table->string('posisi_bermain', 20);
            $table->decimal('tinggi_cm', 5, 1)->nullable();
            $table->decimal('berat_kg', 5, 1)->nullable();
            $table->integer('spike_reach_cm')->nullable();
            $table->integer('block_reach_cm')->nullable();
            $table->boolean('is_captain')->default(false);
            $table->boolean('is_libero')->default(false);
            $table->enum('status_verifikasi', ['PENDING', 'VERIFIED', 'REJECTED'])->default('PENDING');
            $table->timestamps();

            $table->foreign('peserta_tim_id')->references('id')->on('trn_peserta_tim')->onDelete('cascade');
            $table->foreign('pemain_id')->references('id')->on('member_pemain')->onDelete('cascade');
            $table->unique(['peserta_tim_id', 'nomor_punggung'], 'uk_tim_no');
        });

        // 5. Official Tim Turnamen
        Schema::create('trn_official', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('peserta_tim_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('nama_lengkap', 150);
            $table->enum('jabatan', [
                'HEAD_COACH', 'AST_COACH_1', 'AST_COACH_2', 'MANAGER', 
                'AST_MANAGER', 'TRAINER', 'THERAPIST', 'MEDIS', 'TRANSLATOR'
            ]);
            $table->string('nomor_lisensi', 100)->nullable();
            $table->string('scan_lisensi_path', 255)->nullable();
            $table->enum('status_verifikasi', ['PENDING', 'VERIFIED', 'REJECTED'])->default('PENDING');
            $table->timestamps();

            $table->foreign('peserta_tim_id')->references('id')->on('trn_peserta_tim')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('core_users')->onDelete('set null');
        });

        // 6. Jadwal Pertandingan
        Schema::create('trn_jadwal_pertandingan', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('edisi_id');
            $table->integer('nomor_match')->nullable();
            $table->string('fase', 50)->default('PENYISIHAN');
            $table->string('pool_grup', 10)->nullable();
            $table->date('tanggal_pertandingan');
            $table->time('waktu_mulai')->nullable();
            $table->string('venue_lapangan', 100)->nullable();
            $table->unsignedInteger('tim_a_id');
            $table->unsignedInteger('tim_b_id');
            $table->integer('skor_set_a')->default(0);
            $table->integer('skor_set_b')->default(0);
            $table->json('detail_skor_set')->nullable();
            $table->unsignedInteger('tim_pemenang_id')->nullable();
            $table->unsignedBigInteger('wasit_1_id')->nullable();
            $table->unsignedBigInteger('wasit_2_id')->nullable();
            $table->unsignedBigInteger('wasit_cadangan_id')->nullable();
            $table->enum('status_pertandingan', ['TERJADWAL', 'BERLANGSUNG', 'SELESAI', 'DITUNDA'])->default('TERJADWAL');
            $table->timestamps();

            $table->foreign('edisi_id')->references('id')->on('trn_edisi')->onDelete('cascade');
            $table->foreign('tim_a_id')->references('id')->on('trn_peserta_tim')->onDelete('restrict');
            $table->foreign('tim_b_id')->references('id')->on('trn_peserta_tim')->onDelete('restrict');
            $table->foreign('wasit_1_id')->references('id')->on('member_wasit')->onDelete('set null');
            $table->foreign('wasit_2_id')->references('id')->on('member_wasit')->onDelete('set null');
        });

        // 7. Lineup Pertandingan (O-2 Match Lineup)
        Schema::create('trn_lineup_otwo', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('jadwal_id');
            $table->unsignedInteger('peserta_tim_id');
            $table->unsignedBigInteger('roster_id');
            $table->boolean('is_starting_six')->default(false);
            $table->boolean('is_captain_match')->default(false);
            $table->boolean('is_libero_match')->default(false);
            $table->unsignedBigInteger('confirmed_by_user_id')->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('jadwal_id')->references('id')->on('trn_jadwal_pertandingan')->onDelete('cascade');
            $table->foreign('peserta_tim_id')->references('id')->on('trn_peserta_tim')->onDelete('cascade');
            $table->foreign('roster_id')->references('id')->on('trn_roster')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trn_lineup_otwo');
        Schema::dropIfExists('trn_jadwal_pertandingan');
        Schema::dropIfExists('trn_official');
        Schema::dropIfExists('trn_roster');
        Schema::dropIfExists('trn_peserta_tim');
        Schema::dropIfExists('trn_edisi');
        Schema::dropIfExists('trn_master_turnamen');
    }
};
