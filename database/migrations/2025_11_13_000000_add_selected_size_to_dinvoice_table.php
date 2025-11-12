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
        Schema::table('dinvoice', function (Blueprint $table) {
            $table->string('selected_size')->nullable()->after('variant_id')->comment('Ukuran yang dipilih saat checkout (2x3, 3x4, 4x6, 6x8)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dinvoice', function (Blueprint $table) {
            $table->dropColumn('selected_size');
        });
    }
};

