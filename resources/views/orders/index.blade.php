@extends('layout')

@section('title', 'My orders — ' . config('app.name'))

@section('content')
    <div class="mx-auto max-w-3xl">
        <p class="text-xs font-medium uppercase tracking-widest text-neutral-500">Apple products &amp; accessories</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-neutral-900">My orders</h1>
        <p class="mt-2 text-sm text-neutral-500">Track status and payment for your purchases. Totals are in Ghana cedis ({{ config('store.currency_code') }}).</p>

        @if ($orders->isEmpty())
            <div class="mt-12 rounded-2xl border border-neutral-100 bg-white p-12 text-center shadow-sm">
                <p class="text-neutral-500">You have no orders yet.</p>
                <a href="{{ route('home') }}" class="mt-6 inline-block rounded-full bg-primary-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-primary-700">Shop the store</a>
            </div>
        @else
            <ul class="mt-10 space-y-4">
                @foreach ($orders as $order)
                    <li>
                        <a href="{{ route('account.orders.show', $order) }}" class="block rounded-2xl border border-neutral-100 bg-white p-6 shadow-sm transition hover:shadow-md">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm text-neutral-500">Order <span class="font-mono font-medium text-neutral-900">{{ $order->order_number }}</span></p>
                                    <p class="mt-1 text-xs text-neutral-400">{{ $order->created_at->format('M j, Y · g:i A') }}</p>
                                </div>
                                <p class="text-lg font-semibold text-neutral-900">{{ format_ghs($order->total_amount) }}</p>
                            </div>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <span class="inline-flex rounded-full bg-neutral-100 px-3 py-1 text-xs font-medium text-neutral-800">{{ str_replace('_', ' ', $order->delivery_status) }}</span>
                                <span class="inline-flex rounded-full bg-neutral-50 px-3 py-1 text-xs font-medium text-neutral-600">{{ strtoupper($order->payment_method) }}</span>
                                <span class="inline-flex rounded-full bg-neutral-50 px-3 py-1 text-xs font-medium text-neutral-600">{{ $order->payment_status === 'paid' ? 'Paid' : 'Unpaid' }}</span>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="mt-8">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection
