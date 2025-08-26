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
        Schema::create('dinvoice', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hinvoice_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->bigInteger('price');
            $table->bigInteger('quantity');
            $table->bigInteger('subtotal');
            $table->text('kebutuhan_custom')->nullable();
            $table->string('warna_custom')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dinvoice');
    }
};
