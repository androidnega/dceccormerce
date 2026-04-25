@extends('layouts.dashboard')

@section('title', 'Orders — Admin')
@section('heading', 'Orders')
@section('subheading', 'Manage fulfillment and payments.')

@section('content')
    @php
        use App\Support\OrderDeliveryPipeline;
    @endphp
    <div>
        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-neutral-100 bg-neutral-50/80 text-left text-xs font-medium uppercase tracking-wide text-neutral-500">
                        <th class="px-6 py-4">Order #</th>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Rider</th>
                        <th class="px-6 py-4 text-right">Total (GHS)</th>
                        <th class="px-6 py-4">Fulfillment</th>
                        <th class="px-6 py-4">Delivery method</th>
                        <th class="px-6 py-4">Payment method</th>
                        <th class="px-6 py-4">Payment status</th>
                        <th class="px-6 py-4">Delivery status</th>
                        <th class="px-6 py-4">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-neutral-50/50">
                            <td class="px-6 py-4 font-mono text-neutral-900">
                                <a href="{{ route('dashboard.orders.show', $order) }}" class="font-medium hover:underline">{{ $order->order_number }}</a>
                            </td>
                            <td class="px-6 py-4 text-neutral-600">
                                @if ($order->user)
                                    {{ $order->user->email }}
                                @else
                                    <span class="text-neutral-400">Guest</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-neutral-600">
                                {{ $order->rider?->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-neutral-900">{{ format_ghs($order->total_amount) }}</td>
                            <td class="px-6 py-4 text-neutral-700">
                                <span class="text-xs font-medium text-neutral-900" title="{{ OrderDeliveryPipeline::channelDescription(OrderDeliveryPipeline::resolveChannel($order)) }}">
                                    {{ OrderDeliveryPipeline::channelLabel(OrderDeliveryPipeline::resolveChannel($order)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="rounded-full bg-neutral-100 px-2.5 py-0.5 text-xs font-medium text-neutral-800">{{ ucfirst($order->delivery_option ?? 'standard') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="rounded-full bg-neutral-50 px-2.5 py-0.5 text-xs font-medium text-neutral-600">{{ strtoupper($order->payment_method) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="rounded-full bg-neutral-50 px-2.5 py-0.5 text-xs font-medium text-neutral-600">@if ($order->payment_status === 'refunded')Refunded@elseif($order->payment_status === 'paid')Paid@else Unpaid@endif</span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $ds = (string) ($order->delivery_status ?? 'pending');
                                    $delivered = $ds === 'delivered';
                                    $failed = $ds === 'failed';
                                @endphp
                                <span @class([
                                    'inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium',
                                    'bg-emerald-100 text-emerald-900' => $delivered,
                                    'bg-red-100 text-red-800' => $failed,
                                    'bg-amber-50 text-amber-900' => ! $delivered && ! $failed && in_array($ds, ['on_the_way', 'assigned'], true),
                                    'bg-neutral-100 text-neutral-800' => ! $delivered && ! $failed && ! in_array($ds, ['on_the_way', 'assigned'], true),
                                ])>{{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $ds)) }}</span>
                            </td>
                            <td class="px-6 py-4 text-neutral-500">{{ $order->created_at->format('M j, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-neutral-500">No orders yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    </div>
@endsection
