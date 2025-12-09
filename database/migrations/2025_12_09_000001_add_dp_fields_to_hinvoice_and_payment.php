<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hinvoice', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('status');
            $table->timestamp('dp_paid_at')->nullable()->after('dp_amount');
            $table->bigInteger('remaining_amount')->nullable()->after('dp_amount');
            $table->timestamp('remaining_paid_at')->nullable()->after('remaining_amount');
            $table->unsignedBigInteger('remaining_collected_by')->nullable()->after('remaining_paid_at');
            $table->string('midtrans_transaction_id')->nullable()->after('remaining_collected_by');
        });

        Schema::table('payment', function (Blueprint $table) {
            if (!Schema::hasColumn('payment', 'midtrans_id')) {
                $table->string('midtrans_id')->nullable()->after('invoice_id');
            }
            if (!Schema::hasColumn('payment', 'status')) {
                $table->string('status')->nullable()->after('type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hinvoice', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'dp_paid_at',
                'remaining_amount',
                'remaining_paid_at',
                'remaining_collected_by',
                'midtrans_transaction_id',
            ]);
        });

        Schema::table('payment', function (Blueprint $table) {
            if (Schema::hasColumn('payment', 'midtrans_id')) {
                $table->dropColumn('midtrans_id');
            }
            if (Schema::hasColumn('payment', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};

