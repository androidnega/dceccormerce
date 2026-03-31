<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Map legacy status values to the new lifecycle (pending, processing, shipped, delivered, cancelled).
     */
    public function up(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('orders')) {
            return;
        }

        DB::table('orders')->where('status', 'paid')->update(['status' => 'processing']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
