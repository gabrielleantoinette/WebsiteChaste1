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
            $table->string('bahan_custom')->nullable()->after('warna_custom');
            $table->string('ukuran_custom')->nullable()->after('bahan_custom');
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
            $columnsToDrop = [];
            
            // Cek apakah kolom ada sebelum drop
            if (Schema::hasColumn('dinvoice', 'bahan_custom')) {
                $columnsToDrop[] = 'bahan_custom';
            }
            if (Schema::hasColumn('dinvoice', 'ukuran_custom')) {
                $columnsToDrop[] = 'ukuran_custom';
            }
            if (Schema::hasColumn('dinvoice', 'jumlah_ring_custom')) {
                $columnsToDrop[] = 'jumlah_ring_custom';
            }
            if (Schema::hasColumn('dinvoice', 'pakai_tali_custom')) {
                $columnsToDrop[] = 'pakai_tali_custom';
            }
            if (Schema::hasColumn('dinvoice', 'catatan_custom')) {
                $columnsToDrop[] = 'catatan_custom';
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
