@extends('layout')

@section('title', 'Order placed — ' . config('app.name'))

@section('content')
    <div class="max-w-2xl">
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900">
            <p class="font-semibold">Order placed successfully</p>
            <p class="mt-1 text-sm">Thank you. Your order number is <span class="font-mono font-bold">{{ $order->order_number }}</span>.</p>
            @if ($order->payment_status === 'paid' && $order->payment_method === 'momo')
                <p class="mt-2 text-sm">Your payment was applied successfully (Paystack / Mobile Money or card).</p>
            @endif
            <p class="mt-1 text-sm">
                <a href="{{ route('tracking.index') }}" class="font-medium underline">Track your order</a>
                or open
                <a href="{{ route('orders.track', ['order_number' => $order->order_number]).'?token='.urlencode((string) $order->access_token) }}" class="font-mono underline">{{ $order->order_number }}</a>.
            </p>
            @if ($order->access_token)
                <p class="mt-2 text-xs text-slate-600">Save your private link:
                    <a href="{{ route('orders.lookup', ['order_number' => $order->order_number]).'?token='.urlencode((string) $order->access_token) }}" class="font-mono underline break-all">Order details</a>
                </p>
            @endif
        </div>

        @php
            use App\Support\OrderDeliveryPipeline;
            $fulfillmentCh = OrderDeliveryPipeline::resolveChannel($order);
        @endphp
        <div class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900">What happens next</h2>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">
                <span class="font-medium text-slate-800">{{ OrderDeliveryPipeline::channelLabel($fulfillmentCh) }}.</span>
                {{ OrderDeliveryPipeline::channelDescription($fulfillmentCh) }}
            </p>
            <p class="mt-3 text-sm text-slate-600">Use the tracking link below to see each step (confirm → prepare → dispatch → complete).</p>
        </div>

        <div class="mt-8 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Order summary</h2>
            @php
                $itemsSum = $order->items->sum(fn ($i) => (float) $i->price * $i->quantity);
                $promoDisc = (float) ($order->promo_discount_amount ?? 0);
            @endphp
            <ul class="mt-4 divide-y divide-slate-100">
                @foreach ($order->items as $item)
                    <li class="flex justify-between gap-4 py-3 text-sm">
                        <span class="text-slate-800">{{ $item->product?->name ?? 'Product' }} × {{ $item->quantity }}</span>
                        <span class="text-slate-900">{{ format_ghs((float) $item->price * $item->quantity) }}</span>
                    </li>
                @endforeach
            </ul>
            <dl class="mt-4 space-y-2 border-t border-slate-200 pt-4 text-sm text-slate-800">
                <div class="flex justify-between gap-4">
                    <dt>Subtotal</dt>
                    <dd class="tabular-nums font-medium">{{ format_ghs($itemsSum) }}</dd>
                </div>
                @if ($promoDisc > 0.009)
                    <div class="flex justify-between gap-4 text-emerald-800">
                        <dt>Promo discount</dt>
                        <dd class="tabular-nums font-medium">−{{ format_ghs($promoDisc) }}</dd>
                    </div>
                @endif
                @if ((float) ($order->discount_amount ?? 0) > 0.009 && $order->coupon_code)
                    <div class="flex justify-between gap-4 text-emerald-800">
                        <dt>Coupon ({{ $order->coupon_code }})</dt>
                        <dd class="tabular-nums font-medium">−{{ format_ghs((float) $order->discount_amount) }}</dd>
                    </div>
                @endif
                <div class="flex justify-between gap-4 border-t border-slate-100 pt-3 text-base font-semibold text-slate-900">
                    <dt>Total (GHS)</dt>
                    <dd class="tabular-nums">{{ format_ghs($order->total_amount) }}</dd>
                </div>
            </dl>
            <p class="mt-2 text-xs text-slate-500">
                Delivery: {{ $order->delivery_zone ?? '—' }}
                · Option: {{ $order->delivery_option ?? '—' }}
                · Method: {{ ucfirst((string) ($order->delivery_method ?? $order->delivery_option ?? 'rider')) }}
                · Fee: {{ format_ghs((float) ($order->delivery_price ?? 0)) }}
                · Payment: {{ $order->payment_status }}
            </p>
        </div>

        @if ($order->address)
            <div class="mt-8 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Delivery details</h2>
                <div class="mt-3 space-y-4 text-sm text-slate-700">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Order contact</p>
                        <p class="mt-1">{{ $order->address->full_name }}<br>{{ $order->address->phone }}</p>
                    </div>
                    @if ($order->address->recipientDiffersFromContact())
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Recipient</p>
                            <p class="mt-1">{{ $order->address->deliveryRecipientName() }}<br>{{ $order->address->deliveryRecipientPhone() }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Address</p>
                        <p class="mt-1">
                            {{ $order->address->address }}<br>
                            @if ($order->address->city || $order->address->country)
                                {{ trim(implode(', ', array_filter([$order->address->city, $order->address->country]))) }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-8 flex flex-wrap gap-6">
            @auth
                <a href="{{ route('account.orders.index') }}" class="text-sm font-medium text-neutral-900 underline">View my orders</a>
            @endauth
            <a href="{{ route('home') }}" class="text-sm font-medium text-neutral-900 underline">Continue shopping</a>
        </div>
    </div>
@endsection
