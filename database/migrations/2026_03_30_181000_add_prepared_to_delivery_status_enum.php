<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Align MySQL ENUM with {@see \App\Models\Order::DELIVERY_STATUSES} (includes "prepared").
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql' || ! Schema::hasTable('orders')) {
            return;
        }

        DB::statement("ALTER TABLE orders MODIFY delivery_status ENUM('pending','confirmed','prepared','assigned','on_the_way','delivered','failed') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql' || ! Schema::hasTable('orders')) {
            return;
        }

        DB::table('orders')->where('delivery_status', 'prepared')->update(['delivery_status' => 'confirmed']);

        DB::statement("ALTER TABLE orders MODIFY delivery_status ENUM('pending','confirmed','assigned','on_the_way','delivered','failed') NOT NULL DEFAULT 'pending'");
    }
};
