@extends('layouts.rider')

@section('title', 'My deliveries — '.config('app.name'))
@section('heading', 'Assigned orders')
@section('subheading', $rider->name.' · '.ucfirst($rider->vehicle_type))

@section('content')
    <div class="space-y-4">
        @forelse ($orders as $order)
            <a href="{{ route('rider.orders.show', $order) }}" class="block rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-emerald-300 hover:shadow-md active:scale-[0.99] sm:p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="font-mono text-sm font-semibold text-slate-900">{{ $order->order_number }}</p>
                        <p class="mt-1 text-sm text-slate-600">{{ $order->address ? $order->address->deliveryRecipientName() : 'Customer' }}</p>
                        <p class="mt-0.5 text-xs text-slate-500">{{ $order->address?->deliveryRecipientPhone() }}</p>
                    </div>
                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-800">{{ str_replace('_', ' ', $order->delivery_status) }}</span>
                </div>
                <p class="mt-3 line-clamp-2 text-xs text-slate-500">{{ $order->address?->address }}, {{ $order->address?->city }}</p>
            </a>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-200 bg-white px-6 py-14 text-center text-sm text-slate-500">
                No orders assigned to you yet.
            </div>
        @endforelse
    </div>

    @if ($orders->hasPages())
        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @endif
@endsection
