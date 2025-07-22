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
        Schema::create('damaged_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->unsignedBigInteger('return_id')->nullable();
            $table->integer('quantity')->default(1);
            $table->text('damage_description')->nullable();
            $table->string('damage_media_path')->nullable();
            $table->enum('status', ['rusak', 'diperbaiki', 'normal'])->default('rusak');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('set null');
            $table->foreign('return_id')->references('id')->on('returns')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('damaged_products');
    }
}; 