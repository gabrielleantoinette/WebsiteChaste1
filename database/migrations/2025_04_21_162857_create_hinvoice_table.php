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
        Schema::create('hinvoice', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->unsignedBigInteger('gudang_id')->nullable();
            $table->unsignedBigInteger('accountant_id')->nullable();
            $table->bigInteger('grand_total');
            $table->bigInteger('shipping_cost')->default(0);
            $table->string('status');
            $table->string('address')->default('');
            $table->boolean('is_paid')->default(false);
            $table->boolean('is_dp')->default(false);
            $table->boolean('is_online')->default(true);
            $table->bigInteger('dp_amount')->nullable();
            $table->bigInteger('paid_amount')->nullable();
            $table->date('due_date')->nullable();
            $table->date('receive_date')->nullable();
            $table->date('received_date')->nullable();
            $table->string('delivery_proof_photo')->nullable();
            $table->string('delivery_signature')->nullable();
            $table->string('transfer_proof')->nullable();
            $table->string('quality_proof_photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hinvoice');
    }
};
