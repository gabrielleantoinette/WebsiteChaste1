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
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->nullable(); // warna bahan baku
            $table->string('unit')->nullable(); // misal: meter, kg, rol, dll
            $table->decimal('default_price', 12, 2)->nullable();
            $table->decimal('stock', 10, 2)->default(0); // stok dalam meter persegi
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_materials');
    }
};
