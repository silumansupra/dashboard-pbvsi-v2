<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Master Jabatan
        Schema::create('core_master_jabatan', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama_jabatan', 150);
            $table->boolean('is_official_tim')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Master Posisi Pemain
        Schema::create('core_master_posisi_pemain', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama_posisi', 100);
            $table->string('kode_posisi', 10)->unique(); // S, OH, OP, MB, L
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core_master_posisi_pemain');
        Schema::dropIfExists('core_master_jabatan');
    }
};
