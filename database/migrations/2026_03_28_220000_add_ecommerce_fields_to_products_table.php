<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('discount_type', 16)->nullable()->after('price');
            $table->decimal('discount_value', 10, 2)->nullable()->after('discount_type');
            $table->boolean('flash_sale')->default(false)->after('discount_value');
            $table->timestamp('sale_end_time')->nullable()->after('flash_sale');
            $table->boolean('is_featured')->default(false)->after('sale_end_time');
            $table->boolean('is_trending')->default(false)->after('is_featured');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'discount_type',
                'discount_value',
                'flash_sale',
                'sale_end_time',
                'is_featured',
                'is_trending',
            ]);
        });
    }
};
