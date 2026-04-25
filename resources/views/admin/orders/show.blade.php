@extends('layouts.dashboard')

@section('title', 'Order ' . $order->order_number . ' — Admin')
@section('heading', 'Order '.$order->order_number)
@section('subheading', $order->created_at->format('F j, Y · g:i A'))

@section('content')
    @php
        $deliveryMethod = $orderDeliveryMethod ?? ((string) ($order->delivery_method ?? '') !== ''
            ? (string) $order->delivery_method
            : ((($order->delivery_option ?? '') === 'pickup') ? 'pickup' : 'rider'));
        $isPickupLike = in_array($deliveryMethod, ['pickup', 'manual'], true);
    @endphp
    <div>
        <a href="{{ route('dashboard.orders.index') }}" class="text-sm text-neutral-500 hover:text-neutral-900">&larr; All orders</a>

        <div class="mt-6 flex flex-wrap items-start justify-between gap-6">
            <div>
                @if ($order->user)
                    <p class="text-sm text-neutral-700">Customer: {{ $order->user->email }}</p>
                @else
                    <p class="text-sm text-neutral-600">Guest checkout@if ($order->customer_email) — {{ $order->customer_email }}@endif</p>
                @endif
                @if ($order->coupon_code)
                    <p class="mt-1 text-xs text-neutral-600">Coupon <span class="font-mono font-medium">{{ $order->coupon_code }}</span>
                        @if ((float) ($order->discount_amount ?? 0) > 0.009)
                            · −{{ format_ghs((float) $order->discount_amount) }}
                        @endif
                    </p>
                @endif
                <p class="mt-2 text-xs text-neutral-600">Delivery status: <span class="font-medium text-neutral-800">{{ str_replace('_', ' ', $order->delivery_status) }}</span></p>
                <p class="mt-1 text-xs text-neutral-600">
                    Zone <span class="font-medium text-neutral-800">{{ $order->delivery_zone ?: '—' }}</span>
                    · Option <span class="font-medium text-neutral-800">{{ $order->delivery_option ?? '—' }}</span>
                    · Method <span class="font-medium text-neutral-800">{{ str_replace('_', ' ', $deliveryMethod) }}</span>
                    @if ($order->delivery_price !== null)
                        · Delivery fee <span class="font-medium text-neutral-800">{{ format_ghs((float) $order->delivery_price) }}</span>
                    @endif
                </p>
                <p class="mt-1 text-xs text-neutral-600">
                    Payment:
                    <span class="font-medium text-neutral-800">@if ($order->payment_status === 'refunded')Refunded@elseif($order->payment_status === 'paid')Paid@else Unpaid@endif</span>
                    · <span class="text-neutral-600">Method</span> <span class="font-medium text-neutral-800">{{ strtoupper($order->payment_method) }}</span>
                </p>
                @if ($order->paystack_reference)
                    <p class="mt-1 text-xs text-neutral-600">Paystack reference <span class="font-mono font-medium text-neutral-800">{{ $order->paystack_reference }}</span></p>
                @endif
                @if (($order->refund_status ?? 'none') !== 'none' && $order->payment_method === 'momo')
                    <p class="mt-1 text-xs text-neutral-600">Refund: <span class="font-medium text-neutral-800">{{ strtoupper($order->refund_status) }}</span>@if ($order->paystack_refund_id) · ref <span class="font-mono">{{ $order->paystack_refund_id }}</span>@endif @if ($order->refunded_at)<span class="text-neutral-500">({{ $order->refunded_at->format('M j, Y g:i A') }})</span>@endif</p>
                @endif
            </div>
            @php
                $itemsSum = $order->items->sum(fn ($i) => (float) $i->price * $i->quantity);
                $promoDisc = (float) ($order->promo_discount_amount ?? 0);
            @endphp
            <div class="text-right text-sm">
                @if ($promoDisc > 0.009)
                    <p class="text-xs text-neutral-700">Subtotal <span class="font-medium text-neutral-900">{{ format_ghs($itemsSum) }}</span></p>
                    <p class="mt-0.5 text-xs text-emerald-800">Promo −{{ format_ghs($promoDisc) }}</p>
                @endif
                <p class="mt-1 text-xs text-neutral-600">Total (GHS)</p>
                <p class="text-2xl font-semibold text-neutral-900">{{ format_ghs($order->total_amount) }}</p>
            </div>
        </div>

        <div class="mt-8 rounded-2xl border border-neutral-100 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-semibold tracking-wide text-neutral-900">Fulfillment progress</h2>
            <p class="mt-1 text-xs text-neutral-600">
                Steps follow this order’s channel (store pickup, in-house dispatch, or courier). Use the action cards below to move the order forward.
            </p>
            <div class="mt-4 max-w-xl">
                <x-order-delivery-timeline :order="$order" variant="staff" />
            </div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-3">
            <div class="rounded-2xl border border-neutral-100 bg-white p-6 shadow-sm">
                <h2 class="text-sm font-semibold tracking-wide text-neutral-900">Confirm order</h2>
                <form action="{{ route('dashboard.orders.confirm', $order) }}" method="post" class="mt-4 space-y-3">
                    @csrf
                    <button
                        type="submit"
                        class="w-full rounded-full bg-[#0057b8] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#00479a] disabled:bg-[#00479a] disabled:cursor-not-allowed disabled:text-white disabled:opacity-100"
                        @disabled(! $order->canTransitionTo('confirmed'))
                    >
                        Confirm
                    </button>
                </form>
            </div>

            @unless ($isPickupLike)
                <div class="rounded-2xl border border-neutral-100 bg-white p-6 shadow-sm">
                    <h2 class="text-sm font-semibold tracking-wide text-neutral-900">Assign delivery agent</h2>
                    <p class="mt-1 text-xs text-neutral-600">Matches method <span class="font-medium text-neutral-800">{{ str_replace('_', ' ', $deliveryMethod) }}</span> (see Logistics → Delivery agents).</p>
                    @if ($availableAgents->isEmpty())
                        <p class="mt-4 text-sm text-amber-800">No agents for this method. Add one under <a href="{{ route('dashboard.delivery-agents.create') }}" class="font-medium underline">Delivery agents</a>.</p>
                    @else
                        <form action="{{ route('dashboard.orders.assign-delivery-agent', $order) }}" method="post" class="mt-4 space-y-3">
                            @csrf
                            <select name="delivery_agent_id" id="delivery_agent_id" class="w-full rounded-xl border border-neutral-200 bg-white px-4 py-2.5 text-sm text-neutral-900 focus:border-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-400" required>
                                <option value="">Select agent</option>
                                @foreach ($availableAgents as $agent)
                                    <option value="{{ $agent->id }}" @selected((int) $order->delivery_agent_id === (int) $agent->id)>
                                        {{ $agent->name }}
                                        @if ($agent->phone)
                                            ({{ $agent->phone }})
                                        @endif
                                        @if ($agent->vehicle_type)
                                            · {{ $agent->vehicle_type }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('delivery_agent_id')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <button
                                type="submit"
                                class="w-full rounded-full bg-[#0057b8] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#00479a] disabled:bg-[#00479a] disabled:cursor-not-allowed disabled:text-white disabled:opacity-100"
                                @disabled(! $order->canTransitionTo('assigned'))
                            >
                                Assign
                            </button>
                        </form>
                    @endif
                </div>
            @endunless

            <div class="rounded-2xl border border-neutral-100 bg-white p-6 shadow-sm">
                <h2 class="text-sm font-semibold tracking-wide text-neutral-900">Prepare dispatch</h2>
                <form action="{{ route('dashboard.orders.prepare', $order) }}" method="post" class="mt-4 space-y-3">
                    @csrf
                    <button
                        type="submit"
                        class="w-full rounded-full bg-[#0057b8] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#00479a] disabled:bg-[#00479a] disabled:cursor-not-allowed disabled:text-white disabled:opacity-100"
                        @disabled(! $order->canTransitionTo('prepared'))
                    >
                        Prepared
                    </button>
                </form>
            </div>

            <div class="rounded-2xl border border-neutral-100 bg-white p-6 shadow-sm">
                <h2 class="text-sm font-semibold tracking-wide text-neutral-900">Delivery actions</h2>
                @unless ($isPickupLike)
                    <form action="{{ route('dashboard.orders.on-the-way', $order) }}" method="post" class="mt-4">
                        @csrf
                        <button type="submit" class="w-full rounded-full border border-neutral-200 bg-white px-4 py-2.5 text-sm font-medium text-neutral-900 hover:bg-neutral-50" @disabled(! $order->canTransitionTo('on_the_way'))>
                            Mark on the way
                        </button>
                    </form>
                @endunless
                <form action="{{ route('dashboard.orders.deliver', $order) }}" method="post" class="mt-3">
                    @csrf
                    <button type="submit" class="w-full rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-medium text-emerald-800 hover:bg-emerald-100" @disabled(! $order->canTransitionTo('delivered'))>
                        Mark delivered
                    </button>
                </form>
                <form action="{{ route('dashboard.orders.failed', $order) }}" method="post" class="mt-3">
                    @csrf
                    <button type="submit" class="w-full rounded-full border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm font-medium text-rose-800 hover:bg-rose-100" @disabled(! $order->canTransitionTo('failed'))>
                        Mark failed
                    </button>
                </form>
                <form action="{{ route('dashboard.orders.cancel', $order) }}" method="post" class="mt-3" onsubmit="return confirm('Cancel this order and restore stock?');">
                    @csrf
                    <button type="submit" class="w-full rounded-full border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm font-medium text-amber-900 hover:bg-amber-100" @disabled(! in_array($order->delivery_status, ['pending', 'confirmed', 'prepared'], true))>
                        Cancel order
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-8 rounded-2xl border border-neutral-100 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-semibold tracking-wide text-neutral-900">Admin note</h2>
            <form action="{{ route('dashboard.orders.notes', $order) }}" method="post" class="mt-4">
                @csrf
                <textarea name="notes" rows="3" class="w-full rounded-xl border border-neutral-200 bg-white px-4 py-2.5 text-sm text-neutral-900 focus:border-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-400" placeholder="Called customer, confirmed location...">{{ old('notes', $order->notes) }}</textarea>
                <button type="submit" class="mt-3 rounded-full bg-[#0057b8] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#00479a] disabled:bg-[#00479a] disabled:cursor-not-allowed disabled:text-white disabled:opacity-100">Save note</button>
            </form>
        </div>

        @if ($order->deliveryAgent || $order->rider)
            <div class="mt-8 rounded-2xl border border-neutral-100 bg-white p-6 shadow-sm">
                <h2 class="text-sm font-semibold tracking-wide text-neutral-900">Assigned delivery</h2>
                @if ($order->deliveryAgent)
                    <p class="mt-3 text-sm text-neutral-800">
                        {{ $order->deliveryAgent->name }}
                        <span class="text-neutral-600">· {{ $order->deliveryAgent->type }}</span>
                        @if ($order->deliveryAgent->phone)
                            <span class="text-neutral-600">· {{ $order->deliveryAgent->phone }}</span>
                        @endif
                    </p>
                @endif
                @if ($order->rider)
                    <p class="mt-2 text-xs text-neutral-600">Rider app: {{ $order->rider->name }} · {{ $order->rider->phone }} · {{ ucfirst($order->rider->vehicle_type) }}</p>
                @endif
            </div>
        @endif

        <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-neutral-100 bg-white p-8 shadow-sm">
                <h2 class="text-sm font-semibold tracking-wide text-neutral-900">Items</h2>
                <ul class="mt-6 space-y-8">
                    @foreach ($order->items as $item)
                        @php($img = $item->product?->images->first())
                        <li class="flex gap-6">
                            <div class="h-32 w-32 shrink-0 overflow-hidden rounded-2xl bg-neutral-100">
                                @if ($img)
                                    <img src="{{ $img->url() }}" alt="" class="h-full w-full object-cover">
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-neutral-900">{{ $item->product?->name ?? 'Product' }}</p>
                                <p class="mt-1 text-sm text-neutral-700">Quantity {{ $item->quantity }}</p>
                                <p class="mt-1 text-sm text-neutral-700">Unit (GHS) {{ format_ghs($item->price) }}</p>
                                <p class="mt-2 text-sm font-semibold text-neutral-900">Line total (GHS) {{ format_ghs((float) $item->price * $item->quantity) }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            @if ($order->address)
                <div class="rounded-2xl border border-neutral-100 bg-white p-8 shadow-sm">
                    <h2 class="text-sm font-semibold tracking-wide text-neutral-900">Delivery</h2>
                    <div class="mt-4 space-y-4 text-sm leading-relaxed text-neutral-700">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-neutral-500">Order contact</p>
                            <p class="mt-1">{{ $order->address->full_name }}<br>{{ $order->address->phone }}</p>
                        </div>
                        @if ($order->address->recipientDiffersFromContact())
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-neutral-500">Recipient at delivery</p>
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
    </div>
@endsection
