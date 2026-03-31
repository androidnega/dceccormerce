@extends('layouts.rider')

@section('title', 'Order '.$order->order_number.' — Rider')
@section('heading', $order->order_number)
@section('subheading', str_replace('_', ' ', $order->delivery_status))

@section('content')
    <div class="space-y-6">
        <a href="{{ route('rider.dashboard') }}" class="text-sm text-slate-500 hover:text-slate-900">&larr; Back to orders</a>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900">Recipient &amp; contact</h2>
            <p class="mt-1 text-xs text-slate-500">Call the recipient for handoff; order contact is for who placed the order.</p>
            <p class="mt-3 text-base font-medium text-slate-900">{{ $order->address?->deliveryRecipientName() ?? '—' }}</p>
            @if ($order->address?->deliveryRecipientPhone())
                <a href="tel:{{ preg_replace('/\s+/', '', $order->address->deliveryRecipientPhone()) }}" class="mt-1 inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 underline">
                    <i class="fa-solid fa-phone text-xs"></i> {{ $order->address->deliveryRecipientPhone() }}
                </a>
            @endif
            @if ($order->address?->recipientDiffersFromContact())
                <p class="mt-4 border-t border-slate-100 pt-3 text-xs text-slate-600">
                    <span class="font-medium text-slate-800">Order placed by:</span>
                    {{ $order->address->full_name }} · {{ $order->address->phone }}
                </p>
            @endif
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900">Delivery address</h2>
            <p class="mt-2 text-sm leading-relaxed text-slate-700">
                {{ $order->address?->address ?? '—' }}<br>
                {{ $order->address?->city ?? '' }}{{ $order->address?->country ? ', '.$order->address->country : '' }}
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900">Items</h2>
            <ul class="mt-3 divide-y divide-slate-100">
                @foreach ($order->items as $item)
                    @php($img = $item->product?->images->first())
                    <li class="flex gap-3 py-3 first:pt-0">
                        <div class="h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-slate-100">
                            @if ($img)
                                <img src="{{ $img->url() }}" alt="" class="h-full w-full object-cover">
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-slate-900">{{ $item->product?->name ?? 'Product' }}</p>
                            <p class="text-xs text-slate-500">Qty {{ $item->quantity }} · {{ format_ghs($item->price) }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
            <p class="mt-3 border-t border-slate-100 pt-3 text-sm font-semibold text-slate-900">Total {{ format_ghs($order->total_amount) }}</p>
            <p class="mt-1 text-xs text-slate-500">Payment: {{ strtoupper($order->payment_method) }}</p>
        </div>

        <div class="grid gap-3 sm:grid-cols-3">
            <form action="{{ route('rider.orders.on-the-way', $order) }}" method="post">
                @csrf
                <button type="submit" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 shadow-sm hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40" @disabled(! $order->canTransitionTo('on_the_way'))>
                    On the way
                </button>
            </form>
            <form action="{{ route('rider.orders.deliver', $order) }}" method="post">
                @csrf
                <button type="submit" class="w-full rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-40" @disabled(! $order->canTransitionTo('delivered'))>
                    Delivered
                </button>
            </form>
            <form action="{{ route('rider.orders.fail', $order) }}" method="post" onsubmit="return confirm('Mark this delivery as failed?');">
                @csrf
                <button type="submit" class="w-full rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800 hover:bg-rose-100 disabled:cursor-not-allowed disabled:opacity-40" @disabled(! $order->canTransitionTo('failed'))>
                    Failed
                </button>
            </form>
        </div>
    </div>
@endsection
