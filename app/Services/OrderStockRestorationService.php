<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderStockRestorationService
{
    public function restoreIfEligible(Order $order): void
    {
        if ($order->stock_restored_at !== null) {
            return;
        }

        if (! in_array((string) $order->delivery_status, ['failed', 'cancelled'], true)) {
            return;
        }

        DB::transaction(function () use ($order): void {
            $locked = Order::query()->whereKey($order->id)->lockForUpdate()->first();
            if ($locked === null || $locked->stock_restored_at !== null) {
                return;
            }

            $locked->load('items');

            foreach ($locked->items as $item) {
                Product::query()->whereKey($item->product_id)->increment('stock', (int) $item->quantity);
            }

            $locked->forceFill(['stock_restored_at' => now()])->save();
        });
    }
}
