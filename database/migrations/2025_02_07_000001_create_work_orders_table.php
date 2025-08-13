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
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Format: SP-001, SP-002, dst
            $table->date('order_date');
            $table->date('due_date')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['dibuat', 'dikerjakan', 'selesai', 'dibatalkan'])->default('dibuat');
            $table->unsignedBigInteger('created_by'); // Admin yang membuat
            $table->unsignedBigInteger('assigned_to')->nullable(); // Gudang yang ditugaskan
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->timestamps();
            
            $table->foreign('created_by')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
