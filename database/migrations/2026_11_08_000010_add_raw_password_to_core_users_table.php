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
        if (Schema::hasTable('core_users') && !Schema::hasColumn('core_users', 'raw_password')) {
            Schema::table('core_users', function (Blueprint $table) {
                $table->string('raw_password', 255)->nullable()->after('password');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('core_users') && Schema::hasColumn('core_users', 'raw_password')) {
            Schema::table('core_users', function (Blueprint $table) {
                $table->dropColumn('raw_password');
            });
        }
    }
};
