<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah flag kelengkapan profil pada core_users
        Schema::table('core_users', function (Blueprint $table) {
            if (!Schema::hasColumn('core_users', 'is_profile_completed')) {
                $table->boolean('is_profile_completed')->default(false)->after('avatar_path')->index();
            }
        });

        // 2. Tambah kolom KYC dan consent data pada core_user_profiles
        Schema::table('core_user_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('core_user_profiles', 'ktp_path')) {
                $table->string('ktp_path', 255)->nullable()->after('nik');
            }
            if (!Schema::hasColumn('core_user_profiles', 'selfie_ktp_path')) {
                $table->string('selfie_ktp_path', 255)->nullable()->after('ktp_path');
            }
            if (!Schema::hasColumn('core_user_profiles', 'verification_status')) {
                $table->enum('verification_status', ['UNVERIFIED', 'PENDING', 'VERIFIED', 'REJECTED'])
                    ->default('PENDING')
                    ->after('selfie_ktp_path')
                    ->index();
            }
            if (!Schema::hasColumn('core_user_profiles', 'terms_agreed_at')) {
                $table->timestamp('terms_agreed_at')->nullable()->after('kodepos');
            }
            if (!Schema::hasColumn('core_user_profiles', 'terms_agreed_ip')) {
                $table->string('terms_agreed_ip', 45)->nullable()->after('terms_agreed_at');
            }
            if (!Schema::hasColumn('core_user_profiles', 'terms_agreed_version')) {
                $table->string('terms_agreed_version', 20)->default('v1.0')->after('terms_agreed_ip');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('core_user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'ktp_path',
                'selfie_ktp_path',
                'verification_status',
                'terms_agreed_at',
                'terms_agreed_ip',
                'terms_agreed_version',
            ]);
        });

        Schema::table('core_users', function (Blueprint $table) {
            $table->dropColumn('is_profile_completed');
        });
    }
};
