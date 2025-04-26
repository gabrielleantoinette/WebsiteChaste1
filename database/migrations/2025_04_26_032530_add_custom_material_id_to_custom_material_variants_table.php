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
        Schema::table('custom_material_variants', function (Blueprint $table) {
            $table->foreignId('custom_material_id')->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('custom_material_variants', function (Blueprint $table) {
            $table->dropForeign(['custom_material_id']);
            $table->dropColumn('custom_material_id');
        });
    }
};
