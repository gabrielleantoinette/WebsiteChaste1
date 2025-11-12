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
        Schema::table('products', function (Blueprint $table) {
            $table->json('min_price_per_size')->nullable()->after('min_price')->comment('Harga tawar minimum per ukuran: {"2x3": 15000, "3x4": 20000, "4x6": 30000, "6x8": 40000}');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('min_price_per_size');
        });
    }
};
