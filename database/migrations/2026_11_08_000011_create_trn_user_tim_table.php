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
        if (!Schema::hasTable('trn_user_tim')) {
            Schema::create('trn_user_tim', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->integer('peserta_tim_id');
                $table->string('role_tim', 50)->default('ADMIN_TIM');
                $table->boolean('status_aktif')->default(true);
                $table->timestamps();

                $table->index('user_id');
                $table->index('peserta_tim_id');
                $table->unique(['user_id', 'peserta_tim_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trn_user_tim');
    }
};
