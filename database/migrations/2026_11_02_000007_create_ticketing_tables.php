<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Master Event Tiket
        Schema::create('ticket_master_events', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('edisi_id')->nullable();
            $table->string('nama_event', 200);
            $table->string('venue', 150);
            $table->string('kota', 100);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('banner_image_path', 255)->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();

            $table->foreign('edisi_id')->references('id')->on('trn_edisi')->onDelete('set null');
        });

        // 2. Kategori / Tier Tiket
        Schema::create('ticket_tiers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('event_id');
            $table->string('nama_tier', 100); // VIP, Regular, Presale
            $table->string('sesi', 100)->nullable();
            $table->date('tanggal_tiket');
            $table->decimal('harga', 12, 2);
            $table->integer('kuota_total');
            $table->integer('kuota_terjual')->default(0);
            $table->integer('max_per_transaksi')->default(4);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('ticket_master_events')->onDelete('cascade');
        });

        // 3. Pesanan Tiket & Midtrans
        Schema::create('ticket_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 100)->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('nama_pembeli', 150);
            $table->string('email_pembeli', 150);
            $table->string('phone_pembeli', 50);
            $table->string('nik_pembeli', 30)->nullable();
            $table->decimal('total_amount', 15, 2);
            $table->string('payment_method', 50)->nullable();
            $table->enum('payment_status', ['PENDING', 'SETTLEMENT', 'EXPIRE', 'CANCEL', 'DENY'])->default('PENDING');
            $table->string('midtrans_transaction_id', 100)->nullable();
            $table->string('midtrans_snap_token', 255)->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('core_users')->onDelete('set null');
            $table->index('order_code');
        });

        // 4. Item Tiket & QR Code Gate Scan
        Schema::create('ticket_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedInteger('tier_id');
            $table->string('ticket_unique_code', 100)->unique();
            $table->string('qr_code_hash', 100)->unique();
            $table->string('nama_pemegang', 150)->nullable();
            $table->boolean('is_checked_in')->default(false);
            $table->dateTime('checked_in_at')->nullable();
            $table->unsignedBigInteger('gate_scanned_by')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('order_id')->references('id')->on('ticket_orders')->onDelete('cascade');
            $table->foreign('tier_id')->references('id')->on('ticket_tiers')->onDelete('restrict');
            $table->index('qr_code_hash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_items');
        Schema::dropIfExists('ticket_orders');
        Schema::dropIfExists('ticket_tiers');
        Schema::dropIfExists('ticket_master_events');
    }
};
