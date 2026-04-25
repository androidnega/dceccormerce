<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'refund_failed')) {
                $table->boolean('refund_failed')->default(false)->after('refund_status');
            }
        });

        if (Schema::hasTable('orders') && ! Schema::hasIndex('orders', ['paystack_reference'], 'unique')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->unique('paystack_reference');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('orders') && Schema::hasIndex('orders', ['paystack_reference'], 'unique')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropUnique(['paystack_reference']);
            });
        }

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'refund_failed')) {
                $table->dropColumn('refund_failed');
            }
        });
    }
};
