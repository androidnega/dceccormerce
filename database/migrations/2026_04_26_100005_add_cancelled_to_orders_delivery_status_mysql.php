<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql' || ! Schema::hasTable('orders')) {
            return;
        }

        DB::statement("ALTER TABLE orders MODIFY delivery_status ENUM('pending','confirmed','prepared','assigned','on_the_way','delivered','failed','cancelled') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql' || ! Schema::hasTable('orders')) {
            return;
        }

        DB::table('orders')->where('delivery_status', 'cancelled')->update(['delivery_status' => 'failed']);

        DB::statement("ALTER TABLE orders MODIFY delivery_status ENUM('pending','confirmed','prepared','assigned','on_the_way','delivered','failed') NOT NULL DEFAULT 'pending'");
    }
};
