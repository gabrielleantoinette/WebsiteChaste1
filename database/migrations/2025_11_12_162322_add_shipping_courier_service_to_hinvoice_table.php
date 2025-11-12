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
        Schema::table('hinvoice', function (Blueprint $table) {
            $table->string('shipping_courier')->nullable()->after('shipping_cost')->comment('Kurir pengiriman (jne, pos, tiki, kurir)');
            $table->string('shipping_service')->nullable()->after('shipping_courier')->comment('Layanan pengiriman (REG, OKE, YES, dll)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hinvoice', function (Blueprint $table) {
            $table->dropColumn(['shipping_courier', 'shipping_service']);
        });
    }
};
