<section class="iphone-series border-b border-neutral-100 bg-white" aria-labelledby="iphone-series-heading">
    <div class="mx-auto w-full px-4 pb-14 pt-4 sm:px-6 sm:pb-16 sm:pt-6 lg:px-8">
        <div class="text-center">
            <h2 id="iphone-series-heading" class="text-2xl font-bold tracking-tight text-neutral-900 sm:text-3xl">iPhone Series</h2>
            <p class="mt-2 text-sm text-neutral-500 sm:text-base">Check &amp; Get Your Desired Product!</p>
        </div>

        <div class="mt-10 grid grid-cols-1 gap-8 sm:grid-cols-2 sm:gap-6 lg:grid-cols-4 lg:gap-5">
            @foreach ($iphoneSeriesProducts as $product)
                @php
                    $urls = $product->images->map(fn ($img) => $img->url())->filter()->values()->all();
                    $mainUrl = $urls[0] ?? '';
                    $swatches = array_slice($urls, 1, 3);
                @endphp
                <article
                    data-iphone-card
                    class="group relative flex flex-col"
                >
                    <div class="relative aspect-square overflow-hidden bg-neutral-100">
                        @if ($product->stock <= 0)
                            <span class="absolute left-2 top-2 z-20 rounded bg-red-600 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-white shadow-sm">Sold out</span>
                        @endif

                        <a href="{{ route('products.show', $product) }}" class="relative z-0 flex h-full w-full items-center justify-center p-4">
                            @if ($mainUrl !== '')
                                <img
                                    data-iphone-main
                                    src="{{ $mainUrl }}"
                                    alt="{{ $product->name }}"
                                    class="max-h-full max-w-full object-contain transition duration-300 ease-out group-hover:scale-[1.02]"
                                    loading="lazy"
                                    decoding="async"
                                >
                            @else
                                <span class="text-sm text-neutral-400">No image</span>
                            @endif
                        </a>

                        {{-- Hover: actions + color swatches --}}
                        <div
                            class="pointer-events-none absolute inset-0 z-10 flex flex-col justify-between opacity-0 transition-opacity duration-200 group-hover:pointer-events-auto group-hover:opacity-100"
                        >
                            <div class="flex justify-end p-2">
                                <div class="flex flex-col gap-px rounded-sm border border-neutral-200 bg-white p-1 shadow-lg">
                                    <button
                                        type="button"
                                        class="flex h-9 w-9 items-center justify-center text-neutral-500 transition hover:bg-neutral-50 hover:text-red-500"
                                        aria-label="Add to wishlist"
                                        title="Wishlist"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                                    </button>
                                    <button
                                        type="button"
                                        class="flex h-9 w-9 items-center justify-center text-neutral-500 transition hover:bg-neutral-50 hover:text-primary-700"
                                        aria-label="Compare"
                                        title="Compare"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                                    </button>
                                    <a
                                        href="{{ route('products.show', $product) }}"
                                        class="flex h-9 w-9 items-center justify-center text-neutral-500 transition hover:bg-neutral-50 hover:text-primary-700"
                                        aria-label="Quick view"
                                        title="Quick view"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                                    </a>
                                </div>
                            </div>

                            @if (count($swatches) > 0)
                                <div class="flex justify-center gap-2 px-2 pb-3">
                                    @foreach ($swatches as $u)
                                        <button
                                            type="button"
                                            class="iphone-swatch h-9 w-9 overflow-hidden rounded border border-neutral-200 bg-white shadow-sm ring-offset-2 transition hover:ring-2 hover:ring-neutral-300 focus:outline-none focus:ring-2 focus:ring-primary-500"
                                            data-iphone-src="{{ $u }}"
                                            aria-label="Show this color"
                                        >
                                            <img src="{{ $u }}" alt="" class="h-full w-full object-cover">
                                        </button>
                                    @endforeach
                                </div>
                            @else
                                <div></div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 px-0.5">
                        <div class="max-h-0 overflow-hidden transition-[max-height] duration-200 ease-out group-hover:max-h-10">
                            <a
                                href="{{ route('products.show', $product) }}"
                                class="mb-1 block text-sm font-medium text-red-600 hover:text-red-700"
                            >+ Select options</a>
                        </div>
                        <h3 class="text-[15px] font-medium leading-snug text-neutral-900 sm:text-base">
                            <a href="{{ route('products.show', $product) }}" class="hover:text-primary-800">{{ $product->name }}</a>
                        </h3>
                        <p class="mt-1 text-base font-semibold text-neutral-900">{{ format_ghs($product->price) }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

@once
    @push('scripts')
        <script>
            (function () {
                document.querySelectorAll('[data-iphone-card]').forEach(function (card) {
                    card.querySelectorAll('.iphone-swatch').forEach(function (btn) {
                        btn.addEventListener('click', function (e) {
                            e.preventDefault();
                            e.stopPropagation();
                            var src = btn.getAttribute('data-iphone-src');
                            if (!src) return;
                            var main = card.querySelector('[data-iphone-main]');
                            if (main) main.setAttribute('src', src);
                        });
                    });
                });
            })();
        </script>
    @endpush
@endonce
