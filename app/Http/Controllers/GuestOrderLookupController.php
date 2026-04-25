<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuestOrderLookupController extends Controller
{
    public function show(Request $request, string $order_number): View
    {
        $token = trim((string) $request->query('token', ''));
        abort_if($token === '', 403);

        $order = Order::findByOrderNumberAndAccessToken($order_number, $token);
        abort_if($order === null, 403);

        $order->load(['items.product', 'address', 'rider', 'deliveryAgent']);

        return view('guest-order.show', compact('order'));
    }
}
