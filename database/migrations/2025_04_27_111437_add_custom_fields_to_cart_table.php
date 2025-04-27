<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart', function (Blueprint $table) {
            $table->string('kebutuhan_custom')->nullable()->after('variant_id');
            $table->string('ukuran_custom')->nullable()->after('kebutuhan_custom');
            $table->string('warna_custom')->nullable()->after('ukuran_custom');
            $table->integer('jumlah_ring_custom')->nullable()->after('warna_custom');
            $table->string('pakai_tali_custom')->nullable()->after('jumlah_ring_custom');
            $table->text('catatan_custom')->nullable()->after('pakai_tali_custom');
            $table->integer('harga_custom')->nullable()->after('catatan_custom');
        });
    }

    public function down(): void
    {
        Schema::table('cart', function (Blueprint $table) {
            $table->dropColumn([
                'kebutuhan_custom',
                'ukuran_custom',
                'warna_custom',
                'jumlah_ring_custom',
                'pakai_tali_custom',
                'catatan_custom',
                'harga_custom', // <--- ini sekalian bisa di-drop
            ]);
        });
    }
};
