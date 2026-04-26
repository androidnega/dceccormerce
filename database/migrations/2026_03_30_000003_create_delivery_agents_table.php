<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_agents', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            // rider, driver, third_party, pickup, manual
            $table->string('type', 32);

            $table->string('phone')->nullable();
            $table->string('vehicle_type')->nullable();

            // available, busy, offline
            $table->string('status', 32)->default('available');

            // Link to legacy rider accounts so existing rider workflow still works.
            $table->foreignId('rider_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_agents');
    }
};
