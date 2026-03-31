<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_product_display_settings', function (Blueprint $table) {
            $table->id();
            $table->string('product_layout', 32)->default('grid');
            $table->boolean('enable_hover_actions')->default(true);
            $table->boolean('enable_quick_view')->default(true);
            $table->boolean('enable_wishlist')->default(true);
            $table->boolean('enable_image_hover_swap')->default(false);
            $table->string('card_size', 16)->default('medium');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_product_display_settings');
    }
};
