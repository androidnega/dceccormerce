<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Repairs databases where migration 2026_03_28_220000 is marked ran but columns were never created.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'discount_type')) {
                $table->string('discount_type', 16)->nullable()->after('price');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'discount_value')) {
                $table->decimal('discount_value', 10, 2)->nullable();
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'flash_sale')) {
                $table->boolean('flash_sale')->default(false);
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'sale_end_time')) {
                $table->timestamp('sale_end_time')->nullable();
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'is_featured')) {
                $table->boolean('is_featured')->default(false);
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'is_trending')) {
                $table->boolean('is_trending')->default(false);
            }
        });
    }

    public function down(): void
    {
        // Non-destructive repair migration: do not drop columns on rollback.
    }
};
