@props([
    'order',
    'variant' => 'customer', // customer | staff
])

@php
    use App\Support\OrderDeliveryPipeline;

    $p = $variant === 'staff'
        ? OrderDeliveryPipeline::progressForStaff($order)
        : OrderDeliveryPipeline::progressForCustomer($order);

    $steps = $p['steps'];
    $currentIndex = $p['current_index'];
    $isFailed = $p['is_failed'];
    $isDelivered = $p['is_delivered'] ?? ($order->delivery_status === 'delivered');
    $subKey = $variant === 'staff' ? 'staff' : 'buyer';
@endphp

<div {{ $attributes->merge(['class' => '']) }}>
    @if ($variant === 'staff')
        <div class="mb-4 rounded-xl border border-[#cce0f7] bg-[#f0f7ff] px-4 py-3 text-sm">
            <p class="font-semibold text-[#0057b8]">{{ $p['channel_label'] }}</p>
            <p class="mt-1 text-slate-600">{{ $p['channel_description'] }}</p>
        </div>
    @endif

    <ol class="relative space-y-0 border-l-2 border-slate-200 pl-6">
        @foreach ($steps as $i => $step)
            @php
                $isDone = $isDelivered || ($currentIndex !== null && $i < $currentIndex);
                $isActive = ! $isFailed && ! $isDelivered && $currentIndex !== null && $i === $currentIndex;
                $isWaiting = ! $isFailed && ! $isDelivered && $currentIndex !== null && $i > $currentIndex;
            @endphp
            <li class="relative pb-8 last:pb-0">
                <span
                    class="absolute -left-[1.36rem] top-1 flex h-4 w-4 items-center justify-center rounded-full border-2 border-white ring-2 ring-white {{ $isActive ? 'bg-[#0057b8]' : ($isDone ? 'bg-emerald-600' : 'bg-slate-300') }}"
                    aria-hidden="true"
                ></span>
                <p class="text-sm font-semibold text-slate-900">{{ $step['title'] }}</p>
                <p class="mt-0.5 text-xs leading-relaxed {{ $isActive ? 'text-[#0057b8]' : ($isDone ? 'text-emerald-800' : 'text-slate-500') }}">
                    {{ $step[$subKey] ?? '' }}
                </p>
                <p class="mt-1 text-[11px] uppercase tracking-wide {{ $isWaiting ? 'text-amber-700' : ($isActive ? 'text-[#0057b8]' : ($isDone ? 'text-emerald-700' : 'text-slate-400')) }}">
                    @if ($isWaiting)
                        Upcoming
                    @elseif ($isActive)
                        Current step
                    @else
                        {{ $isDone ? 'Done' : '—' }}
                    @endif
                </p>
            </li>
        @endforeach
    </ol>

    @if ($isFailed)
        <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
            <p class="font-semibold">Delivery issue</p>
            <p class="mt-1 text-rose-800/90">
                @if ($variant === 'customer')
                    This order could not be completed as planned. Please contact the store.
                @else
                    Marked failed. Previous step was {{ str_replace('_', ' ', (string) ($order->failed_previous_status ?? 'unknown')) }}.
                @endif
            </p>
        </div>
    @endif
</div>
