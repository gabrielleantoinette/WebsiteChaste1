<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, clean up any existing records with variant_id = 0 (set to null)
        DB::table('cart')->where('variant_id', 0)->update(['variant_id' => null]);
        
        Schema::table('cart', function (Blueprint $table) {
            // Drop foreign key first if it exists
            $table->dropForeign(['variant_id']);
        });
        
        Schema::table('cart', function (Blueprint $table) {
            // Make variant_id nullable
            $table->unsignedBigInteger('variant_id')->nullable()->change();
        });
        
        Schema::table('cart', function (Blueprint $table) {
            // Re-add foreign key with cascade (NULL values are allowed)
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['variant_id']);
        });
        
        Schema::table('cart', function (Blueprint $table) {
            // Make variant_id not nullable again
            $table->unsignedBigInteger('variant_id')->nullable(false)->change();
        });
        
        Schema::table('cart', function (Blueprint $table) {
            // Re-add foreign key
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
        });
    }
};
