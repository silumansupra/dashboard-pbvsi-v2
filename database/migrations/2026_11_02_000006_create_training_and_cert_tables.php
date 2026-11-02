<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Kegiatan Kursus / Pelatihan Wasit & Pelatih
        Schema::create('kursus_kegiatan', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('bidang_kursus', ['WASIT', 'PELATIH']);
            $table->string('level_tingkat', 50);
            $table->unsignedInteger('pengprov_id')->nullable();
            $table->text('lokasi_kursus')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->decimal('biaya_pendaftaran', 15, 2)->default(0.00);
            $table->enum('status_kegiatan', ['TERJADWAL', 'BERJALAN', 'SELESAI', 'BATAL'])->default('TERJADWAL');
            $table->string('surat_tugas_path', 255)->nullable();
            $table->string('link_pendaftaran', 255)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('pengprov_id')->references('id')->on('org_pengprov')->onDelete('set null');
        });

        // 2. Peserta Kursus
        Schema::create('kursus_peserta', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('kegiatan_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('status_pembayaran', ['PENDING', 'LUNAS', 'GRATIS'])->default('PENDING');
            $table->decimal('nilai_pretest', 5, 2)->nullable();
            $table->decimal('nilai_posttest', 5, 2)->nullable();
            $table->decimal('nilai_praktek', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->enum('status_kelulusan', ['PENDING', 'LULUS', 'TIDAK_LULUS'])->default('PENDING');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('kegiatan_id')->references('id')->on('kursus_kegiatan')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('core_users')->onDelete('cascade');
        });

        // 3. Materi Kursus
        Schema::create('kursus_materi', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('kegiatan_id');
            $table->string('judul_materi', 150);
            $table->text('deskripsi')->nullable();
            $table->string('file_path', 255);
            $table->integer('urutan')->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('kegiatan_id')->references('id')->on('kursus_kegiatan')->onDelete('cascade');
        });

        // 4. Template Sertifikat
        Schema::create('sertifikat_template', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode_template', 50)->unique();
            $table->string('nama_template', 150);
            $table->enum('tipe_sertifikat', ['KEJURNAS_JUARA', 'KEJURNAS_PESERTA', 'KURSUS_WASIT', 'KURSUS_PELATIH', 'PENGHARGAAN']);
            $table->string('background_image_path', 255);
            $table->json('layout_config')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Sertifikat Terbit (Ber-QR Code)
        Schema::create('sertifikat_terbit', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('template_id');
            $table->string('nomor_sertifikat', 150)->unique();
            $table->string('uuid_verifikasi', 64)->unique();
            $table->enum('penerima_tipe', ['PEMAIN', 'OFFICIAL', 'WASIT', 'PELATIH', 'TIM']);
            $table->unsignedBigInteger('penerima_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedInteger('edisi_id')->nullable();
            $table->unsignedInteger('kegiatan_kursus_id')->nullable();
            $table->string('nama_penerima', 200);
            $table->string('predikat', 100)->nullable();
            $table->date('tanggal_terbit');
            $table->string('qr_code_path', 255)->nullable();
            $table->string('pdf_file_path', 255)->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();

            $table->foreign('template_id')->references('id')->on('sertifikat_template')->onDelete('restrict');
            $table->foreign('user_id')->references('id')->on('core_users')->onDelete('set null');
            $table->foreign('edisi_id')->references('id')->on('trn_edisi')->onDelete('set null');
            $table->foreign('kegiatan_kursus_id')->references('id')->on('kursus_kegiatan')->onDelete('set null');
            $table->index('uuid_verifikasi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sertifikat_terbit');
        Schema::dropIfExists('sertifikat_template');
        Schema::dropIfExists('kursus_materi');
        Schema::dropIfExists('kursus_peserta');
        Schema::dropIfExists('kursus_kegiatan');
    }
};
