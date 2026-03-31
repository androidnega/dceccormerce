@php
    /** @var int $pi */
    /** @var array $slot */
    /** @var string $previewUrl */
    /** @var string $bgHex */
@endphp

<div class="flex flex-col rounded-2xl border border-neutral-200/90 bg-gradient-to-b from-neutral-50/90 to-white p-5 shadow-sm ring-1 ring-neutral-900/[0.04]">
    <div class="flex items-start justify-between gap-3 border-b border-neutral-200/80 pb-4">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-neutral-500">Slot {{ $pi + 1 }}</p>
            <p class="mt-1 text-sm font-semibold text-neutral-900">Promo card</p>
        </div>

        @if ($previewUrl !== '')
            <div class="h-14 w-24 shrink-0 overflow-hidden rounded-lg border border-neutral-200 bg-neutral-100 shadow-inner">
                <img src="{{ $previewUrl }}" alt="" class="h-full w-full object-cover object-center">
            </div>
        @else
            <div class="flex h-14 w-24 shrink-0 items-center justify-center rounded-lg border border-dashed border-neutral-300 bg-white text-[10px] font-medium text-neutral-400">
                No image
            </div>
        @endif
    </div>

    <div class="mt-4 space-y-4">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-wide text-neutral-500">Headlines</p>
            <div class="mt-2 grid gap-3">
                <div>
                    <label class="block text-xs font-medium text-neutral-700" for="promo_{{ $pi }}_t1">Title line 1</label>
                    <input
                        id="promo_{{ $pi }}_t1"
                        type="text"
                        name="promo_banners[{{ $pi }}][title_line1]"
                        value="{{ old("promo_banners.$pi.title_line1", $slot['title_line1'] ?? '') }}"
                        required
                        class="mt-1 w-full rounded-lg border border-neutral-200 bg-white px-3 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/25"
                    >
                    @error("promo_banners.$pi.title_line1")<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-neutral-700" for="promo_{{ $pi }}_t2">Title line 2</label>
                    <input
                        id="promo_{{ $pi }}_t2"
                        type="text"
                        name="promo_banners[{{ $pi }}][title_line2]"
                        value="{{ old("promo_banners.$pi.title_line2", $slot['title_line2'] ?? '') }}"
                        required
                        class="mt-1 w-full rounded-lg border border-neutral-200 bg-white px-3 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/25"
                    >
                    @error("promo_banners.$pi.title_line2")<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div>
            <p class="text-[11px] font-semibold uppercase tracking-wide text-neutral-500">Price &amp; fallback color</p>
            <div class="mt-2 space-y-3">
                <div>
                    <label class="block text-xs font-medium text-neutral-700" for="promo_{{ $pi }}_price">Price (GHS / ₵)</label>
                    <input
                        id="promo_{{ $pi }}_price"
                        type="text"
                        name="promo_banners[{{ $pi }}][price_label]"
                        value="{{ old("promo_banners.$pi.price_label", $slot['price_label'] ?? '') }}"
                        required
                        placeholder="e.g. FROM ₵ 319"
                        class="mt-1 w-full rounded-lg border border-neutral-200 bg-white px-3 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/25"
                    >
                    @error("promo_banners.$pi.price_label")<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-neutral-700" for="promo_{{ $pi }}_bg_hex">Background hex</label>
                    <div class="mt-1 flex flex-wrap items-center gap-2">
                        <input
                            type="color"
                            id="promo_{{ $pi }}_bg_picker"
                            value="{{ $bgHex }}"
                            class="h-10 w-14 cursor-pointer rounded-lg border border-neutral-300 bg-white p-0.5 shadow-sm"
                            aria-label="Pick fallback background for card {{ $pi + 1 }}"
                            data-promo-bg-picker="{{ $pi }}"
                        >
                        <input
                            type="text"
                            name="promo_banners[{{ $pi }}][background_hex]"
                            id="promo_{{ $pi }}_bg_hex"
                            value="{{ $bgHex }}"
                            required
                            pattern="#[0-9A-Fa-f]{6}"
                            placeholder="#d8cdc0"
                            class="min-w-0 flex-1 rounded-lg border border-neutral-200 bg-white px-3 py-2 font-mono text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/25 sm:max-w-[9.5rem]"
                            data-promo-bg-hex="{{ $pi }}"
                        >
                    </div>
                    @error("promo_banners.$pi.background_hex")<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-neutral-700" for="promo_{{ $pi }}_link">Link URL</label>
            <input
                id="promo_{{ $pi }}_link"
                type="text"
                name="promo_banners[{{ $pi }}][link_url]"
                value="{{ old("promo_banners.$pi.link_url", $slot['link_url'] ?? '/') }}"
                class="mt-1 w-full rounded-lg border border-neutral-200 bg-white px-3 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/25"
                placeholder="/ or https://… or #store-search">
            @error("promo_banners.$pi.link_url")<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-3">
            <label class="block text-xs font-medium text-neutral-700" for="promo_{{ $pi }}_file">Banner image</label>
            <input
                id="promo_{{ $pi }}_file"
                type="file"
                name="promo_banners[{{ $pi }}][image]"
                accept="image/*"
                class="mt-2 block w-full text-sm text-neutral-600 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-indigo-500"
            >

            @if ($previewUrl !== '')
                <div class="mt-3 overflow-hidden rounded-lg border border-neutral-200 bg-neutral-50">
                    <div class="aspect-[21/9] w-full">
                        <img src="{{ $previewUrl }}" alt="" class="h-full w-full object-contain object-center p-2">
                    </div>
                </div>
            @endif

            @error("promo_banners.$pi.image")<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>
</div>

