<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Wilayah Provinsi
        Schema::create('core_wilayah_provinsi', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode_provinsi', 10)->nullable()->unique();
            $table->string('nama_provinsi', 100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('nama_provinsi');
        });

        // 2. Wilayah Kabupaten / Kota
        Schema::create('core_wilayah_kabupaten', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('provinsi_id');
            $table->string('kode_kabupaten', 10)->nullable();
            $table->string('nama_kabupaten', 100);
            $table->enum('tipe', ['KOTA', 'KABUPATEN'])->default('KABUPATEN');
            $table->timestamps();

            $table->foreign('provinsi_id')->references('id')->on('core_wilayah_provinsi')->onDelete('cascade');
            $table->index(['provinsi_id', 'nama_kabupaten']);
        });

        // 3. Core Users
        Schema::create('core_users', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 64)->unique();
            $table->string('username', 100)->unique();
            $table->string('name', 255);
            $table->string('email', 150)->nullable()->unique();
            $table->string('phone', 50)->nullable();
            $table->string('password', 255);
            $table->boolean('is_active')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('avatar_path', 255)->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index('phone');
        });

        // 4. Core Roles (Spatie Compatible)
        Schema::create('core_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('guard_name', 50)->default('web');
            $table->string('display_name', 150)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        // 5. Core Permissions
        Schema::create('core_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('guard_name', 50)->default('web');
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        // 6. Model Has Roles
        Schema::create('core_model_has_roles', function (Blueprint $table) {
            $table->unsignedInteger('role_id');
            $table->string('model_type', 150)->default('App\\Models\\User');
            $table->unsignedBigInteger('model_id');

            $table->primary(['role_id', 'model_id', 'model_type']);
            $table->foreign('role_id')->references('id')->on('core_roles')->onDelete('cascade');
            $table->index(['model_id', 'model_type']);
        });

        // 7. Role Has Permissions
        Schema::create('core_role_has_permissions', function (Blueprint $table) {
            $table->unsignedInteger('permission_id');
            $table->unsignedInteger('role_id');

            $table->primary(['permission_id', 'role_id']);
            $table->foreign('permission_id')->references('id')->on('core_permissions')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('core_roles')->onDelete('cascade');
        });

        // 8. User Profiles
        Schema::create('core_user_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('nik', 30)->nullable();
            $table->string('npwp', 30)->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('golongan_darah', 5)->nullable();
            $table->text('alamat')->nullable();
            $table->unsignedInteger('provinsi_id')->nullable();
            $table->unsignedInteger('kabupaten_id')->nullable();
            $table->string('kodepos', 10)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('core_users')->onDelete('cascade');
            $table->foreign('provinsi_id')->references('id')->on('core_wilayah_provinsi')->onDelete('set null');
            $table->foreign('kabupaten_id')->references('id')->on('core_wilayah_kabupaten')->onDelete('set null');
            $table->index('nik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core_user_profiles');
        Schema::dropIfExists('core_role_has_permissions');
        Schema::dropIfExists('core_model_has_roles');
        Schema::dropIfExists('core_permissions');
        Schema::dropIfExists('core_roles');
        Schema::dropIfExists('core_users');
        Schema::dropIfExists('core_wilayah_kabupaten');
        Schema::dropIfExists('core_wilayah_provinsi');
    }
};
