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
            $table->string('ukuran_custom')->nullable()->after('warna_custom');
            $table->integer('jumlah_ring_custom')->nullable()->after('ukuran_custom');
            $table->string('pakai_tali_custom')->nullable()->after('jumlah_ring_custom');
            $table->text('catatan_custom')->nullable()->after('pakai_tali_custom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dinvoice', function (Blueprint $table) {
            $table->dropColumn([
                'ukuran_custom',
                'jumlah_ring_custom',
                'pakai_tali_custom',
                'catatan_custom',
            ]);
        });
    }
};
