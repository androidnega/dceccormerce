<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_addresses', function (Blueprint $table) {
            $table->string('recipient_name')->nullable()->after('phone');
            $table->string('recipient_phone', 50)->nullable()->after('recipient_name');
        });
    }

    public function down(): void
    {
        Schema::table('order_addresses', function (Blueprint $table) {
            $table->dropColumn(['recipient_name', 'recipient_phone']);
        });
    }
};
