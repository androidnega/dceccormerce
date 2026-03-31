@extends('layouts.dashboard')

@section('title', 'Operations — '.config('app.name'))
@section('heading', 'Operations')
@section('subheading', 'Orders, riders, and delivery agents.')

@section('content')
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <p class="text-xs font-medium uppercase tracking-wider text-slate-500">Pending deliveries</p>
            <p class="mt-2 text-3xl font-semibold tabular-nums text-slate-900">{{ number_format($pendingOrders) }}</p>
            <a href="{{ route('dashboard.orders.index') }}" class="mt-3 inline-block text-sm font-medium text-[#ff6000] underline decoration-orange-200 underline-offset-2 hover:text-orange-700">View all orders</a>
        </div>
        <div class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <p class="text-xs font-medium uppercase tracking-wider text-slate-500">Quick links</p>
            <ul class="mt-3 space-y-2 text-sm">
                <li><a href="{{ route('dashboard.riders.index') }}" class="font-medium text-[#ff6000] underline decoration-orange-200 underline-offset-2 hover:text-orange-700">Riders</a></li>
                <li><a href="{{ route('dashboard.delivery-agents.index') }}" class="font-medium text-[#ff6000] underline decoration-orange-200 underline-offset-2 hover:text-orange-700">Delivery agents</a></li>
            </ul>
        </div>
    </div>

    <div class="mt-8 overflow-x-auto rounded-xl border border-slate-200/90 bg-white shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="text-base font-semibold text-slate-900">Recent orders</h2>
            <p class="mt-1 text-sm text-slate-500">Latest activity — open an order to update status.</p>
        </div>
        <table class="min-w-full divide-y divide-zinc-100 text-left text-sm">
            <thead class="bg-zinc-50 text-xs font-semibold uppercase tracking-wide text-zinc-500">
                <tr>
                    <th class="px-4 py-3">Order</th>
                    <th class="px-4 py-3">Customer</th>
                    <th class="px-4 py-3">Total</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">When</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 bg-white">
                @forelse ($recentOrders as $ro)
                    <tr>
                        <td class="whitespace-nowrap px-4 py-3 font-mono text-xs">
                            <a href="{{ route('dashboard.orders.show', $ro) }}" class="font-medium text-[#0057b8] underline decoration-[#cce0f7] underline-offset-2 hover:text-[#00479a]">{{ $ro->order_number }}</a>
                        </td>
                        <td class="max-w-[10rem] truncate px-4 py-3 text-zinc-600">{{ $ro->user?->name ?? '—' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 tabular-nums text-zinc-900">₵ {{ number_format((float) $ro->total_amount, 2) }}</td>
                        <td class="px-4 py-3 text-zinc-600">{{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $ro->delivery_status)) }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-zinc-500">{{ $ro->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-zinc-500">No orders yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
