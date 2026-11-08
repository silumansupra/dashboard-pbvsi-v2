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
        Schema::dropIfExists('core_audit_logs');

        Schema::create('core_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable()->index();
            $table->string('user_name', 255)->nullable();
            $table->string('user_username', 100)->nullable()->index();
            $table->string('user_role', 50)->nullable()->index();
            $table->string('module', 50)->index(); // auth, team, master, personel, user, system
            $table->string('action', 100)->index(); // auth.login, auth.reset_password, etc.
            $table->text('description');
            $table->string('ip_address', 45)->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_audit_logs');
    }
};
