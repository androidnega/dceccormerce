<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_rules', function (Blueprint $table) {
            $table->id();

            // Zone name (e.g. Accra, Takoradi, Outside City)
            $table->string('zone', 64);

            // Fulfillment method (rider, driver, third_party, pickup, manual)
            $table->string('method', 32);

            // Customer-facing option (standard, express, pickup)
            $table->string('option', 32);

            $table->decimal('price', 10, 2)->default(0);
            $table->string('estimated_time', 64)->nullable();
            $table->boolean('active')->default(true);

            $table->timestamps();

            $table->index(['zone', 'method', 'option']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_rules');
    }
};
