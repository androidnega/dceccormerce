<?php

use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('riders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('vehicle_type');
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_number')->nullable()->after('id');
            $table->string('delivery_status')->default('pending')->after('status');
            $table->string('payment_method')->default('cod')->after('payment_status');
            $table->text('notes')->nullable()->after('payment_method');
            $table->foreignId('rider_id')->nullable()->after('user_id')->constrained('riders')->nullOnDelete();
        });

        if (Schema::hasTable('orders')) {
            $orders = DB::table('orders')->orderBy('id')->get(['id', 'created_at', 'status', 'order_number']);
            $yearCounters = [];

            foreach ($orders as $order) {
                $year = $order->created_at ? (int) date('Y', strtotime((string) $order->created_at)) : (int) now()->format('Y');
                $yearCounters[$year] = ($yearCounters[$year] ?? 0) + 1;
                $orderNumber = sprintf('DCA-%d-%04d', $year, $yearCounters[$year]);

                $status = in_array($order->status, Order::DELIVERY_STATUSES, true) ? $order->status : 'pending';

                DB::table('orders')
                    ->where('id', $order->id)
                    ->update([
                        'order_number' => $orderNumber,
                        'delivery_status' => $status,
                    ]);
            }
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->unique('order_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['rider_id']);
            $table->dropUnique(['order_number']);
            $table->dropColumn(['order_number', 'delivery_status', 'payment_method', 'notes', 'rider_id']);
        });

        Schema::dropIfExists('riders');
    }
};
