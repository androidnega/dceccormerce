<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type', 32);
            $table->string('value', 255)->default('');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        DB::table('promos')->insert([
            [
                'title' => 'Free nationwide delivery this week — no minimum.',
                'type' => 'free_delivery',
                'value' => '1',
                'is_active' => true,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Spring sale: extra 5% off your cart at checkout.',
                'type' => 'discount',
                'value' => '5',
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Trade-in bonus — ask us about store credit on your old device.',
                'type' => 'banner',
                'value' => '/products#store-search',
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
