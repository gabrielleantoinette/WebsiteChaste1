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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // Jenis notifikasi (order_new, payment_received, retur_request, etc)
            $table->string('title'); // Judul notifikasi
            $table->text('message'); // Pesan notifikasi
            $table->string('recipient_type'); // employee, customer
            $table->unsignedBigInteger('recipient_id'); // ID penerima
            $table->string('recipient_role')->nullable(); // Role penerima (admin, driver, gudang, etc)
            $table->string('data_type')->nullable(); // Tipe data terkait (order, payment, retur, etc)
            $table->unsignedBigInteger('data_id')->nullable(); // ID data terkait
            $table->boolean('is_read')->default(false); // Status dibaca
            $table->timestamp('read_at')->nullable(); // Waktu dibaca
            $table->string('action_url')->nullable(); // URL untuk aksi notifikasi
            $table->string('icon')->nullable(); // Icon notifikasi
            $table->string('priority')->default('normal'); // Priority: low, normal, high, urgent
            $table->timestamps();
            
            // Indexes
            $table->index(['recipient_type', 'recipient_id']);
            $table->index(['recipient_role']);
            $table->index(['is_read']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
