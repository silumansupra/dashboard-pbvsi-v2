<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sys_migration_maps', function (Blueprint $table) {
            $table->id();
            $table->string('source_database', 100);
            $table->string('source_table', 100);
            $table->string('source_id', 100);
            $table->string('target_table', 100);
            $table->unsignedBigInteger('target_id');
            $table->string('status', 50)->default('SUCCESS');
            $table->text('notes')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamp('migrated_at')->useCurrent();

            $table->index(['source_database', 'source_table', 'source_id'], 'idx_source');
            $table->index(['target_table', 'target_id'], 'idx_target');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sys_migration_maps');
    }
};
