<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'region_id')) {
                $table->foreignId('region_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('orders', 'delivery_zone_id')) {
                $table->foreignId('delivery_zone_id')->nullable()->after('region_id')->constrained('delivery_zones')->nullOnDelete();
            }
            if (! Schema::hasColumn('orders', 'delivery_fee')) {
                $table->decimal('delivery_fee', 10, 2)->nullable()->after('delivery_price');
            }
            if (! Schema::hasColumn('orders', 'customer_email')) {
                $table->string('customer_email')->nullable()->after('paystack_reference');
            }
            if (! Schema::hasColumn('orders', 'access_token')) {
                $table->string('access_token', 64)->nullable()->unique()->after('customer_email');
            }
            if (! Schema::hasColumn('orders', 'coupon_code')) {
                $table->string('coupon_code', 64)->nullable()->after('promo_discount_amount');
            }
            if (! Schema::hasColumn('orders', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->nullable()->after('coupon_code');
            }
            if (! Schema::hasColumn('orders', 'stock_restored_at')) {
                $table->timestamp('stock_restored_at')->nullable()->after('refunded_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'stock_restored_at')) {
                $table->dropColumn('stock_restored_at');
            }
            if (Schema::hasColumn('orders', 'discount_amount')) {
                $table->dropColumn('discount_amount');
            }
            if (Schema::hasColumn('orders', 'coupon_code')) {
                $table->dropColumn('coupon_code');
            }
            if (Schema::hasColumn('orders', 'access_token')) {
                $table->dropUnique(['access_token']);
                $table->dropColumn('access_token');
            }
            if (Schema::hasColumn('orders', 'customer_email')) {
                $table->dropColumn('customer_email');
            }
            if (Schema::hasColumn('orders', 'delivery_fee')) {
                $table->dropColumn('delivery_fee');
            }
            if (Schema::hasColumn('orders', 'delivery_zone_id')) {
                $table->dropConstrainedForeignId('delivery_zone_id');
            }
            if (Schema::hasColumn('orders', 'region_id')) {
                $table->dropConstrainedForeignId('region_id');
            }
        });
    }
};
