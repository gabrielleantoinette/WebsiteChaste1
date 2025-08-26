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
        Schema::create('work_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_order_id');
            $table->unsignedBigInteger('raw_material_id'); // ID bahan baku
            $table->string('size_material'); // Ukuran + Bahan (contoh: A2 2x3)
            $table->string('color'); // Warna (contoh: BS Cap GSY)
            $table->integer('quantity'); // Jumlah yang dipotong
            $table->text('remarks')->nullable(); // Keterangan (contoh: Dikoli=50)
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->integer('completed_quantity')->default(0);
            $table->text('notes')->nullable(); // Catatan khusus untuk item ini
            $table->timestamps();
            
            $table->foreign('work_order_id')->references('id')->on('work_orders')->onDelete('cascade');
            $table->foreign('raw_material_id')->references('id')->on('raw_materials')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_order_items');
    }
};
