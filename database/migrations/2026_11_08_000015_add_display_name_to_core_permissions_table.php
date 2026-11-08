<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('core_permissions', function (Blueprint $table) {
            if (!Schema::hasColumn('core_permissions', 'display_name')) {
                $table->string('display_name', 150)->nullable()->after('name');
            }
            if (!Schema::hasColumn('core_permissions', 'description')) {
                $table->string('description', 255)->nullable()->after('display_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('core_permissions', function (Blueprint $table) {
            $table->dropColumn(['display_name', 'description']);
        });
    }
};
