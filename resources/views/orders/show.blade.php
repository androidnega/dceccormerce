@extends('layout')

@section('title', 'Order ' . $order->order_number . ' — ' . config('app.name'))

@section('content')
    <div class="mx-auto w-full min-w-0 max-w-3xl">
        <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
            <a href="{{ route('account.orders.index') }}" class="text-sm text-neutral-500 hover:text-neutral-900">&larr; My orders</a>
            @if ($order->access_token)
                <a href="{{ route('orders.track', ['order_number' => $order->order_number]).'?token='.urlencode((string) $order->access_token) }}" class="text-sm text-neutral-700 underline">Track delivery</a>
            @endif
        </div>

        @php
            use App\Support\OrderDeliveryPipeline;
            $pipe = OrderDeliveryPipeline::progressForCustomer($order);
        @endphp

        <div class="mt-6 rounded-2xl border border-neutral-100 bg-white p-8 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-medium uppercase tracking-widest text-neutral-400">Order</p>
                    <h1 class="mt-1 font-mono text-2xl font-semibold text-neutral-900">{{ $order->order_number }}</h1>
                    <p class="mt-2 text-sm text-neutral-500">{{ $order->created_at->format('F j, Y · g:i A') }}</p>
                    <p class="mt-2 text-sm text-neutral-600">{{ $pipe['channel_label'] }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-neutral-400">Total (GHS)</p>
                    <p class="text-2xl font-semibold text-neutral-900">{{ format_ghs($order->total_amount) }}</p>
                </div>
            </div>

            <div class="mt-8 flex flex-wrap gap-2 border-t border-neutral-100 pt-8">
                <span class="inline-flex rounded-full bg-neutral-100 px-4 py-1.5 text-xs font-medium text-neutral-800">Status: {{ str_replace('_', ' ', $order->delivery_status) }}</span>
                <span class="inline-flex rounded-full bg-neutral-50 px-4 py-1.5 text-xs font-medium text-neutral-600">{{ strtoupper($order->payment_method) }}</span>
                <span class="inline-flex rounded-full bg-neutral-50 px-4 py-1.5 text-xs font-medium text-neutral-600">Payment: @if ($order->payment_status === 'refunded')Refunded@elseif($order->payment_status === 'paid')Paid@else Unpaid@endif</span>
            </div>
            @if ($order->rider)
                <div class="mt-4 rounded-xl border border-neutral-100 bg-neutral-50 p-4 text-sm text-neutral-700">
                    Rider: {{ $order->rider->name }} · {{ $order->rider->phone }} ({{ ucfirst($order->rider->vehicle_type) }})
                </div>
            @endif
        </div>

        <div class="mt-8 rounded-2xl border border-neutral-100 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-semibold tracking-wide text-neutral-900">Delivery progress</h2>
            <div class="mt-4">
                <x-order-delivery-timeline :order="$order" variant="customer" />
            </div>
        </div>

        <div class="mt-8 rounded-2xl border border-neutral-100 bg-white p-8 shadow-sm">
            <h2 class="text-sm font-semibold tracking-wide text-neutral-900">Items</h2>
            <ul class="mt-6 space-y-8">
                @foreach ($order->items as $item)
                    @php($img = $item->product?->images->first())
                    <li class="flex gap-6">
                        <div class="h-28 w-28 shrink-0 overflow-hidden rounded-2xl bg-neutral-100">
                            @if ($img)
                                <img src="{{ $img->url() }}" alt="" class="h-full w-full object-cover">
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-neutral-900">{{ $item->product?->name ?? 'Product' }}</p>
                            <p class="mt-1 text-sm text-neutral-500">Qty {{ $item->quantity }} · {{ format_ghs($item->price) }} each</p>
                            <p class="mt-2 text-sm font-semibold text-neutral-900">{{ format_ghs((float) $item->price * $item->quantity) }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        @if ($order->address)
            <div class="mt-8 rounded-2xl border border-neutral-100 bg-white p-8 shadow-sm">
                <h2 class="text-sm font-semibold tracking-wide text-neutral-900">Delivery</h2>
                <div class="mt-4 space-y-4 text-sm leading-relaxed text-neutral-600">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-neutral-500">Order contact</p>
                        <p class="mt-1">{{ $order->address->full_name }}<br>{{ $order->address->phone }}</p>
                    </div>
                    @if ($order->address->recipientDiffersFromContact())
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-neutral-500">Recipient</p>
                            <p class="mt-1">{{ $order->address->deliveryRecipientName() }}<br>{{ $order->address->deliveryRecipientPhone() }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-neutral-500">Address</p>
                        <p class="mt-1">
                            {{ $order->address->address }}<br>
                            {{ trim(implode(', ', array_filter([$order->address->city, $order->address->country]))) }}
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
