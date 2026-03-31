<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->where('user_id', $request->user()->id)
            ->with(['rider'])
            ->latest()
            ->paginate(12);

        return view('orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless(
            $order->user_id !== null && (int) $order->user_id === (int) $request->user()->id,
            403
        );

        $order->load(['items.product.images', 'address', 'rider', 'deliveryAgent']);

        return view('orders.show', compact('order'));
    }
}
