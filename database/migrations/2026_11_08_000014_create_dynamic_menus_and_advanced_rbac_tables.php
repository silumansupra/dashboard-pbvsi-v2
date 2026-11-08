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
        // 1. Tabel core_menus untuk manajemen menu & submenu dinamis
        if (!Schema::hasTable('core_menus')) {
            Schema::create('core_menus', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('parent_id')->nullable()->index();
                $table->string('title', 100);
                $table->string('route_name', 100)->nullable();
                $table->string('url', 255)->nullable();
                $table->string('icon', 100)->nullable();
                $table->string('permission_key', 100)->nullable()->index();
                $table->integer('order')->default(0)->index();
                $table->boolean('is_active')->default(true)->index();
                $table->string('badge_text', 50)->nullable();
                $table->string('badge_class', 50)->nullable();
                $table->string('target', 20)->default('_self');
                $table->timestamps();

                $table->foreign('parent_id')->references('id')->on('core_menus')->onDelete('cascade');
            });
        }

        // 2. Tabel core_model_has_permissions untuk hak akses individual (User Granular Permissions)
        if (!Schema::hasTable('core_model_has_permissions')) {
            Schema::create('core_model_has_permissions', function (Blueprint $table) {
                $table->integer('permission_id');
                $table->string('model_type', 150)->default('App\\Models\\User');
                $table->unsignedBigInteger('model_id');

                $table->primary(['permission_id', 'model_id', 'model_type'], 'core_model_has_permissions_primary');
                $table->foreign('permission_id')->references('id')->on('core_permissions')->onDelete('cascade');
                $table->index(['model_id', 'model_type']);
            });
        }

        // 3. Kolom audit verifikasi KYC pada core_user_profiles
        Schema::table('core_user_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('core_user_profiles', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('verification_status');
            }
            if (!Schema::hasColumn('core_user_profiles', 'verified_by')) {
                $table->unsignedBigInteger('verified_by')->nullable()->after('verified_at');
            }
            if (!Schema::hasColumn('core_user_profiles', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('verified_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('core_user_profiles', function (Blueprint $table) {
            $table->dropColumn(['verified_at', 'verified_by', 'rejection_reason']);
        });
        Schema::dropIfExists('core_model_has_permissions');
        Schema::dropIfExists('core_menus');
    }
};
