<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('member_official')) {
            Schema::create('member_official', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedInteger('pengprov_id')->nullable();
                $table->unsignedInteger('klub_id')->nullable();
                $table->string('nama_lengkap', 150);
                $table->string('nik', 30)->nullable();
                $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
                $table->string('jabatan_official', 50)->default('MANAGER');
                $table->string('nomor_lisensi', 100)->nullable();
                $table->boolean('status_aktif')->default(true);
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('core_users')->onDelete('cascade');
                $table->foreign('pengprov_id')->references('id')->on('org_pengprov')->onDelete('set null');
                $table->foreign('klub_id')->references('id')->on('org_klub')->onDelete('set null');
                $table->index('nama_lengkap');
                $table->index('nik');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('member_official');
    }
};
