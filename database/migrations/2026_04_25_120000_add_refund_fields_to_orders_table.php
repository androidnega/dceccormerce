<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'refund_status')) {
                $table->string('refund_status', 32)->default('none')->after('paystack_reference');
            }
            if (! Schema::hasColumn('orders', 'paystack_refund_id')) {
                $table->string('paystack_refund_id', 64)->nullable()->after('refund_status');
            }
            if (! Schema::hasColumn('orders', 'refunded_at')) {
                $table->timestamp('refunded_at')->nullable()->after('paystack_refund_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'refunded_at')) {
                $table->dropColumn('refunded_at');
            }
            if (Schema::hasColumn('orders', 'paystack_refund_id')) {
                $table->dropColumn('paystack_refund_id');
            }
            if (Schema::hasColumn('orders', 'refund_status')) {
                $table->dropColumn('refund_status');
            }
        });
    }
};
