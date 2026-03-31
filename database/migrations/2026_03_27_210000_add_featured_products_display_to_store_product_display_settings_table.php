<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_product_display_settings', function (Blueprint $table) {
            $table->string('featured_products_display', 24)->default('grid')->after('card_size');
        });
    }

    public function down(): void
    {
        Schema::table('store_product_display_settings', function (Blueprint $table) {
            $table->dropColumn('featured_products_display');
        });
    }
};
