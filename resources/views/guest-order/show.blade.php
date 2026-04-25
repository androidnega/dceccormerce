@extends('layout')

@section('title', 'Order '.$order->order_number.' — '.config('app.name'))

@section('content')
    <div class="max-w-2xl">
        <h1 class="text-2xl font-semibold text-slate-900">Order {{ $order->order_number }}</h1>
        <p class="mt-2 text-sm text-slate-600">Payment: {{ $order->payment_status }} · Delivery: {{ str_replace('_', ' ', $order->delivery_status) }}</p>

        <div class="mt-8 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Items</h2>
            <ul class="mt-4 divide-y divide-slate-100">
                @foreach ($order->items as $item)
                    <li class="flex justify-between gap-4 py-3 text-sm">
                        <span>{{ $item->product?->name ?? 'Product' }} × {{ $item->quantity }}</span>
                        <span>{{ format_ghs((float) $item->price * $item->quantity) }}</span>
                    </li>
                @endforeach
            </ul>
            <p class="mt-4 text-base font-semibold text-slate-900">Total {{ format_ghs((float) $order->total_amount) }}</p>
        </div>

        @if ($order->address)
            <div class="mt-6 rounded-lg border border-slate-200 bg-white p-6 shadow-sm text-sm text-slate-700">
                <h2 class="font-semibold text-slate-900">Delivery</h2>
                <p class="mt-2">{{ $order->address->address }}</p>
                @if ($order->address->city)
                    <p>{{ $order->address->city }}</p>
                @endif
            </div>
        @endif

        <p class="mt-8 text-sm">
            <a href="{{ route('orders.track', ['order_number' => $order->order_number]).'?token='.urlencode((string) $order->access_token) }}" class="font-medium text-slate-900 underline">Track delivery</a>
        </p>
    </div>
@endsection
