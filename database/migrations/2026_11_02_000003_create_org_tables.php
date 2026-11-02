<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pengurus Provinsi (Pengprov)
        Schema::create('org_pengprov', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('provinsi_id');
            $table->string('nama_pengprov', 150);
            $table->string('ketum', 150)->nullable();
            $table->string('sekum', 150)->nullable();
            $table->string('nohp_ketum', 50)->nullable();
            $table->string('nohp_sekum', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('no_telp', 50)->nullable();
            $table->string('website', 150)->nullable();
            $table->text('alamat')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();

            $table->foreign('provinsi_id')->references('id')->on('core_wilayah_provinsi')->onDelete('restrict');
        });

        // 2. Pengurus Kota/Kabupaten (Pengkot)
        Schema::create('org_pengkot', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pengprov_id');
            $table->unsignedInteger('kabupaten_id');
            $table->string('nama_pengkot', 150);
            $table->string('ketum', 150)->nullable();
            $table->string('sekum', 150)->nullable();
            $table->string('nohp_ketum', 50)->nullable();
            $table->string('nohp_sekum', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('no_telp', 50)->nullable();
            $table->text('alamat')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();

            $table->foreign('pengprov_id')->references('id')->on('org_pengprov')->onDelete('cascade');
            $table->foreign('kabupaten_id')->references('id')->on('core_wilayah_kabupaten')->onDelete('restrict');
        });

        // 3. Klub Resmi Bola Voli
        Schema::create('org_klub', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pengprov_id')->nullable();
            $table->unsignedInteger('pengkot_id')->nullable();
            $table->string('kode_klub', 50)->nullable()->unique();
            $table->string('nama_klub', 150);
            $table->string('pemilik_klub', 150)->nullable();
            $table->string('sekretaris_klub', 150)->nullable();
            $table->text('alamat')->nullable();
            $table->string('logo_path', 255)->nullable();
            $table->enum('status_verifikasi', ['PENDING', 'VERIFIED', 'REJECTED'])->default('PENDING');
            $table->timestamps();

            $table->foreign('pengprov_id')->references('id')->on('org_pengprov')->onDelete('set null');
            $table->foreign('pengkot_id')->references('id')->on('org_pengkot')->onDelete('set null');
            $table->index('nama_klub');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('org_klub');
        Schema::dropIfExists('org_pengkot');
        Schema::dropIfExists('org_pengprov');
    }
};
