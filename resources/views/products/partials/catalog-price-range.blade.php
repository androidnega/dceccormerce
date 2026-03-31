@php
    $curLow = $minPrice !== null ? (int) round(max($sliderMin, min($sliderMax, $minPrice))) : $sliderMin;
    $curHigh = $maxPrice !== null ? (int) round(min($sliderMax, max($sliderMin, $maxPrice))) : $sliderMax;
    if ($curLow > $curHigh) {
        [$curLow, $curHigh] = [$curHigh, $curLow];
    }
    $atFullRange = $minPrice === null && $maxPrice === null;
@endphp
<div
    class="w-full min-w-0"
    data-catalog-price-range
    data-live-price-submit="1"
    data-slider-min="{{ $sliderMin }}"
    data-slider-max="{{ $sliderMax }}"
    data-step="{{ $priceSliderStep }}"
    data-currency="{{ config('store.currency_code', 'GHS') }}"
>
    {{-- Row 1: min / max (full width; labels avoid squeezing the track) --}}
    <div class="mb-2 flex flex-wrap items-baseline justify-between gap-x-3 gap-y-1 sm:mb-2 lg:mb-3">
        <div class="flex min-w-0 max-w-[48%] flex-1 items-baseline gap-1.5 sm:max-w-none sm:gap-2">
            <span class="shrink-0 text-[10px] font-semibold uppercase tracking-wide text-slate-400">Min</span>
            <span
                class="min-w-0 truncate text-sm font-semibold tabular-nums text-slate-800 sm:text-xs"
                data-range-label-low
            >
                {{ '₵'.number_format((float) $curLow, 0) }}
            </span>
        </div>
        <div class="flex min-w-0 max-w-[48%] flex-1 items-baseline justify-end gap-1.5 sm:max-w-none sm:gap-2">
            <span class="shrink-0 text-[10px] font-semibold uppercase tracking-wide text-slate-400">Max</span>
            <span
                class="min-w-0 truncate text-right text-sm font-semibold tabular-nums text-slate-800 sm:text-xs"
                data-range-label-high
            >
                {{ '₵'.number_format((float) $curHigh, 0) }}
            </span>
        </div>
    </div>
    {{-- Row 2: full-width track --}}
    <div class="catalog-range-track relative min-h-[2.25rem] w-full min-w-0 sm:min-h-[2.5rem] lg:min-h-[2.75rem]">
        <div class="pointer-events-none absolute left-0 right-0 top-1/2 h-1.5 -translate-y-1/2 rounded-full bg-slate-200" aria-hidden="true"></div>
        <input
            type="range"
            data-range-low
            class="catalog-range-input absolute inset-x-0 top-0 z-20 h-11 w-full cursor-pointer appearance-none bg-transparent sm:h-7"
            min="{{ $sliderMin }}"
            max="{{ $sliderMax }}"
            step="{{ $priceSliderStep }}"
            value="{{ $curLow }}"
            aria-label="Minimum price"
        >
        <input
            type="range"
            data-range-high
            class="catalog-range-input absolute inset-x-0 top-0 z-30 h-11 w-full cursor-pointer appearance-none bg-transparent sm:h-7"
            min="{{ $sliderMin }}"
            max="{{ $sliderMax }}"
            step="{{ $priceSliderStep }}"
            value="{{ $curHigh }}"
            aria-label="Maximum price"
        >
    </div>
    <input type="hidden" name="min_price" value="{{ $atFullRange ? '' : $curLow }}" data-range-hidden-min @disabled($atFullRange)>
    <input type="hidden" name="max_price" value="{{ $atFullRange ? '' : $curHigh }}" data-range-hidden-max @disabled($atFullRange)>
</div>

@push('scripts')
        <style>
            .catalog-range-input { pointer-events: none; }
            .catalog-range-input::-webkit-slider-thumb { pointer-events: auto; -webkit-appearance: none; appearance: none; width: 18px; height: 18px; border-radius: 9999px; background: #0057b8; border: 2px solid #fff; box-shadow: 0 1px 2px rgba(0,87,184,0.3); }
            .catalog-range-input::-moz-range-thumb { pointer-events: auto; width: 18px; height: 18px; border-radius: 9999px; background: #0057b8; border: 2px solid #fff; box-shadow: 0 1px 2px rgba(0,87,184,0.3); }
            @media (min-width: 640px) {
                .catalog-range-input::-webkit-slider-thumb { width: 16px; height: 16px; }
                .catalog-range-input::-moz-range-thumb { width: 16px; height: 16px; }
            }
        </style>
        <script>
            (function () {
                var root = document.querySelector('[data-catalog-price-range]');
                if (!root) return;
                var min = parseInt(root.getAttribute('data-slider-min'), 10);
                var max = parseInt(root.getAttribute('data-slider-max'), 10);
                var step = parseInt(root.getAttribute('data-step'), 10) || 1;
                var low = root.querySelector('[data-range-low]');
                var high = root.querySelector('[data-range-high]');
                var labLow = root.querySelector('[data-range-label-low]');
                var labHigh = root.querySelector('[data-range-label-high]');
                var hidMin = root.querySelector('[data-range-hidden-min]');
                var hidMax = root.querySelector('[data-range-hidden-max]');
                if (!low || !high || !hidMin || !hidMax) return;

                function fmt(n) {
                    return '₵' + Number(n).toLocaleString(undefined, { maximumFractionDigits: 0 });
                }

                function clamp(v) {
                    v = Math.round(v / step) * step;
                    return Math.min(max, Math.max(min, v));
                }

                function sync() {
                    var a = parseInt(low.value, 10);
                    var b = parseInt(high.value, 10);
                    if (a > b) {
                        if (document.activeElement === low) high.value = String(a);
                        else low.value = String(b);
                        a = parseInt(low.value, 10);
                        b = parseInt(high.value, 10);
                    }
                    a = clamp(a);
                    b = clamp(b);
                    low.value = String(a);
                    high.value = String(b);
                    if (labLow) labLow.textContent = fmt(a);
                    if (labHigh) labHigh.textContent = fmt(b);
                    var full = a <= min && b >= max;
                    hidMin.disabled = full;
                    hidMax.disabled = full;
                    if (full) {
                        hidMin.value = '';
                        hidMax.value = '';
                    } else {
                        hidMin.disabled = false;
                        hidMax.disabled = false;
                        hidMin.value = String(a);
                        hidMax.value = String(b);
                    }
                }

                var form = root.closest('form');
                var liveSubmit = root.getAttribute('data-live-price-submit') === '1' && form;
                var debounceMs = 380;
                var debounceTimer = null;

                function submitFilterForm() {
                    if (!liveSubmit || !form) return;
                    if (form.requestSubmit) {
                        form.requestSubmit();
                    } else {
                        form.submit();
                    }
                }

                function scheduleLiveSubmit() {
                    if (!liveSubmit) return;
                    if (debounceTimer) clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function () {
                        debounceTimer = null;
                        submitFilterForm();
                    }, debounceMs);
                }

                function onRangeInput() {
                    sync();
                    scheduleLiveSubmit();
                }

                low.addEventListener('input', onRangeInput);
                high.addEventListener('input', onRangeInput);
                low.addEventListener('change', function () {
                    sync();
                    if (!liveSubmit) return;
                    if (debounceTimer) clearTimeout(debounceTimer);
                    debounceTimer = null;
                    submitFilterForm();
                });
                high.addEventListener('change', function () {
                    sync();
                    if (!liveSubmit) return;
                    if (debounceTimer) clearTimeout(debounceTimer);
                    debounceTimer = null;
                    submitFilterForm();
                });

                sync();
            })();
        </script>
@endpush
