<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('failed_previous_status')->nullable()->after('delivery_status');
        });

        if (DB::getDriverName() === 'mysql' && Schema::hasTable('orders')) {
            DB::statement("ALTER TABLE orders MODIFY delivery_status ENUM('pending','confirmed','prepared','assigned','on_the_way','delivered','failed') NOT NULL DEFAULT 'pending'");
            DB::statement("ALTER TABLE orders MODIFY payment_method ENUM('cod','momo') NOT NULL DEFAULT 'cod'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql' && Schema::hasTable('orders')) {
            DB::statement("ALTER TABLE orders MODIFY delivery_status VARCHAR(255) NOT NULL DEFAULT 'pending'");
            DB::statement("ALTER TABLE orders MODIFY payment_method VARCHAR(255) NOT NULL DEFAULT 'cod'");
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('failed_previous_status');
        });
    }
};
