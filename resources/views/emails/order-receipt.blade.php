<x-mail::message>
# Thank you for your order

**Order number:** {{ $order->order_number }}

@if ($order->payment_status === 'paid')
Your payment status: **Paid**
@else
Payment status: **Unpaid** (cash on delivery)
@endif

## Items

@foreach ($order->items as $item)
- {{ $item->product?->name ?? 'Product' }} × {{ $item->quantity }} — {{ format_ghs((float) $item->price * $item->quantity) }}
@endforeach

@php
    $itemsSum = $order->items->sum(fn ($i) => (float) $i->price * $i->quantity);
@endphp

**Subtotal:** {{ format_ghs($itemsSum) }}

@if ((float) ($order->promo_discount_amount ?? 0) > 0.009)
**Promo discount:** −{{ format_ghs((float) $order->promo_discount_amount) }}
@endif

@if ((float) ($order->discount_amount ?? 0) > 0.009)
**Coupon ({{ $order->coupon_code }}):** −{{ format_ghs((float) $order->discount_amount) }}
@endif

**Delivery:** {{ format_ghs((float) ($order->delivery_price ?? 0)) }} ({{ $order->delivery_zone ?? '—' }})

## Total

**{{ format_ghs((float) $order->total_amount) }}**

@if ($order->address)
## Delivery

{{ $order->address->full_name }} — {{ $order->address->phone }}

{{ $order->address->address }}

@if ($order->address->city || $order->address->country)
{{ trim(implode(', ', array_filter([$order->address->city, $order->address->country]))) }}
@endif
@endif

<x-mail::button :url="route('orders.track', ['order_number' => $order->order_number]).'?token='.urlencode((string) $order->access_token)">
Track your order
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
