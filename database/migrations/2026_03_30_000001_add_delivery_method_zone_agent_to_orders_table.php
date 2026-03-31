<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'delivery_method')) {
                $table->string('delivery_method', 32)->nullable()->after('delivery_status');
            }

            if (! Schema::hasColumn('orders', 'delivery_zone')) {
                $table->string('delivery_zone', 64)->nullable()->after('delivery_method');
            }

            if (! Schema::hasColumn('orders', 'delivery_price')) {
                $table->decimal('delivery_price', 10, 2)->default(0)->after('delivery_zone');
            }

            if (! Schema::hasColumn('orders', 'delivery_agent_id')) {
                // We'll add the foreign-key constraint in a later migration once `delivery_agents` exists.
                $table->unsignedBigInteger('delivery_agent_id')->nullable()->after('rider_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'delivery_agent_id')) {
                $table->dropColumn('delivery_agent_id');
            }

            $table->dropColumn([
                'delivery_price',
                'delivery_zone',
                'delivery_method',
            ]);
        });
    }
};

