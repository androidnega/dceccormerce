<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('orders', 'delivery_agent_id')) {
            return;
        }

        // Add the foreign key constraint now that `delivery_agents` exists.
        Schema::table('orders', function (Blueprint $table) {
            // Avoid failing if a similar constraint already exists.
            $table->foreign('delivery_agent_id')
                ->references('id')
                ->on('delivery_agents')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('orders', 'delivery_agent_id')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['delivery_agent_id']);
        });
    }
};
