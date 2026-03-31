@extends('layout')

@section('title', 'Track order ' . $order->order_number . ' — ' . config('app.name'))

@php
    use App\Support\OrderDeliveryPipeline;
    $p = OrderDeliveryPipeline::progressForCustomer($order);
@endphp

@section('content')
    <div class="mx-auto max-w-lg px-4 sm:max-w-xl sm:px-0">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-neutral-900 sm:text-3xl">Track your order</h1>
                <p class="mt-1 font-mono text-sm font-semibold text-neutral-800">{{ $order->order_number }}</p>
                <p class="mt-2 text-sm text-neutral-600">{{ $p['channel_label'] }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="inline-flex rounded-full bg-neutral-100 px-3 py-1 text-xs font-medium text-neutral-800">{{ str_replace('_', ' ', $order->delivery_status) }}</span>
                <span class="inline-flex rounded-full bg-neutral-50 px-3 py-1 text-xs font-medium text-neutral-700">{{ strtoupper($order->payment_method) }}</span>
            </div>
        </div>

        <div class="mt-8 rounded-2xl border border-neutral-100 bg-white p-5 shadow-sm sm:p-6">
            <x-order-delivery-timeline :order="$order" variant="customer" />
        </div>

        @if ($order->rider)
            <div class="mt-6 rounded-2xl border border-neutral-100 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-semibold tracking-wide text-neutral-900">Your rider</h2>
                <p class="mt-2 text-base font-medium text-neutral-900">{{ $order->rider->name }}</p>
                <a href="tel:{{ preg_replace('/\s+/', '', $order->rider->phone) }}" class="mt-1 inline-flex text-sm font-medium text-primary-600 underline decoration-primary-300 underline-offset-2">{{ $order->rider->phone }}</a>
                <p class="mt-2 text-xs text-neutral-500">{{ ucfirst($order->rider->vehicle_type) }}</p>
            </div>
        @endif

        @if ($order->deliveryAgent && ! $order->rider)
            <div class="mt-6 rounded-2xl border border-neutral-100 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-semibold tracking-wide text-neutral-900">Delivery partner</h2>
                <p class="mt-2 text-base font-medium text-neutral-900">{{ $order->deliveryAgent->name }}</p>
                @if ($order->deliveryAgent->phone)
                    <a href="tel:{{ preg_replace('/\s+/', '', $order->deliveryAgent->phone) }}" class="mt-1 inline-flex text-sm font-medium text-primary-600 underline">{{ $order->deliveryAgent->phone }}</a>
                @endif
            </div>
        @endif

        @if ($order->notes)
            <div class="mt-6 rounded-2xl border border-neutral-100 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-semibold tracking-wide text-neutral-900">Message from the store</h2>
                <p class="mt-2 text-sm leading-relaxed text-neutral-700">{{ $order->notes }}</p>
            </div>
        @endif

        <p class="mt-4 text-center text-xs text-neutral-400">Updates automatically when your status changes.</p>

        <p class="mt-8 text-center text-sm text-neutral-500">
            <a href="{{ route('tracking.index') }}" class="font-medium text-primary-600 underline decoration-primary-300 underline-offset-2">Track another order</a>
        </p>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            var statusUrl = @json(route('orders.track.status', ['order_number' => $order->order_number]));
            var last = @json($order->delivery_status);
            setInterval(function () {
                fetch(statusUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (data && data.delivery_status && data.delivery_status !== last) {
                            window.location.reload();
                        }
                    })
                    .catch(function () { /* ignore */ });
            }, 15000);
        })();
    </script>
@endpush
