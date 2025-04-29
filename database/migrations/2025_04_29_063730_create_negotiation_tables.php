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
        Schema::create('negotiation_tables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id');
            $table->string('status');
            $table->bigInteger('final_price');
            $table->bigInteger('cust_nego_1')->nullable();
            $table->bigInteger('cust_nego_2')->nullable();
            $table->bigInteger('cust_nego_3')->nullable();
            $table->bigInteger('seller_nego_1')->nullable();
            $table->bigInteger('seller_nego_2')->nullable();
            $table->bigInteger('seller_nego_3')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('negotiation_tables');
    }
};
