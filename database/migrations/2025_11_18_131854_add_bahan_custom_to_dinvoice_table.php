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
            // Cek apakah kolom sudah ada, jika belum tambahkan
            if (!Schema::hasColumn('dinvoice', 'bahan_custom')) {
                $table->string('bahan_custom')->nullable()->after('warna_custom');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dinvoice', function (Blueprint $table) {
            if (Schema::hasColumn('dinvoice', 'bahan_custom')) {
                $table->dropColumn('bahan_custom');
            }
        });
    }
};
