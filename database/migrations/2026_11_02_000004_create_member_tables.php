<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Master Pemain (Single Source of Truth)
        Schema::create('member_pemain', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedInteger('klub_id')->nullable();
            $table->unsignedInteger('pengprov_id')->nullable();
            $table->string('pbvsi_player_id', 50)->nullable()->unique();
            $table->string('nik', 30)->nullable();
            $table->string('nisn', 30)->nullable();
            $table->string('nama_lengkap', 150);
            $table->string('nama_punggung', 100)->nullable();
            $table->enum('jenis_kelamin', ['PA', 'PI']);
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('posisi_utama', 50)->nullable();
            $table->enum('status_kewarganegaraan', ['WNI_DAERAH', 'WNI_NASIONAL', 'WNA_ASING'])->default('WNI_DAERAH');
            $table->string('foto_profil_path', 255)->nullable();
            $table->string('scan_akta_path', 255)->nullable();
            $table->string('scan_ijazah_path', 255)->nullable();
            $table->string('scan_kk_path', 255)->nullable();
            $table->enum('status_verifikasi', ['PENDING', 'VERIFIED', 'REJECTED'])->default('PENDING');
            $table->text('catatan_verifikasi')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('core_users')->onDelete('set null');
            $table->foreign('klub_id')->references('id')->on('org_klub')->onDelete('set null');
            $table->foreign('pengprov_id')->references('id')->on('org_pengprov')->onDelete('set null');
            $table->index('nik');
            $table->index('nama_lengkap');
            $table->index('tanggal_lahir');
        });

        // 2. Master Wasit
        Schema::create('member_wasit', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('pengprov_id')->nullable();
            $table->string('nomor_lisensi_wasit', 100)->nullable()->unique();
            $table->enum('kategori_wasit', ['INDOOR', 'VOLI_PANTAI'])->default('INDOOR');
            $table->string('tingkat_wasit', 50)->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->text('catatan_perwasitan')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('core_users')->onDelete('cascade');
            $table->foreign('pengprov_id')->references('id')->on('org_pengprov')->onDelete('set null');
        });

        // 3. Master Pelatih
        Schema::create('member_pelatih', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('pengprov_id')->nullable();
            $table->string('nomor_lisensi_pelatih', 100)->nullable()->unique();
            $table->string('tingkat_pelatih', 50)->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('core_users')->onDelete('cascade');
            $table->foreign('pengprov_id')->references('id')->on('org_pengprov')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_pelatih');
        Schema::dropIfExists('member_wasit');
        Schema::dropIfExists('member_pemain');
    }
};
