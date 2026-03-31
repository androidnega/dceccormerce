@php
    use App\Models\StoreProductDisplaySetting;

    $pd = $productDisplay ?? StoreProductDisplaySetting::current();
    $featuredCatalogCarousel = $featuredCatalogCarousel ?? false;
    $featuredCatalogAutoplay = $featuredCatalogAutoplay ?? false;
    $featuredShowcaseId = $featuredShowcaseId ?? 'store-featured-showcase';
    $mode = $featuredCatalogCarousel
        ? StoreProductDisplaySetting::FEATURED_CAROUSEL
        : ($pd->featured_products_display ?? StoreProductDisplaySetting::FEATURED_GRID);
@endphp

<section @class(['pb-10', 'store-box' => !($featuredBare ?? false)]) aria-labelledby="featured-products-heading">
    <h2 id="featured-products-heading" class="text-center text-2xl font-semibold text-gray-900">Featured products</h2>
    <p class="mx-auto mt-2 max-w-lg text-center text-sm text-gray-500">New arrivals and hand-picked highlights.</p>

    @if ($mode === StoreProductDisplaySetting::FEATURED_CAROUSEL)
        <div
            class="store-featured-scroll store-scrollbar-none mt-8 flex snap-x snap-mandatory gap-6 overflow-x-auto pb-2"
            @if ($featuredCatalogAutoplay) data-store-featured-carousel-autoplay @endif
        >
            @foreach ($featuredProducts as $product)
                <div class="store-featured-carousel-item w-[min(100%,280px)] shrink-0 snap-start sm:w-72">
                    @include('products.partials.product-card', ['product' => $product, 'productDisplay' => $pd])
                </div>
            @endforeach
        </div>
        @if ($featuredCatalogAutoplay)
            @push('scripts')
                <script>
                    (function () {
                        var el = document.querySelector('[data-store-featured-carousel-autoplay]');
                        if (!el) return;
                        var items = el.querySelectorAll('.store-featured-carousel-item');
                        if (items.length <= 1) return;
                        var gap = 24;
                        var intervalMs = 4500;
                        var timer = null;
                        function step() {
                            var first = items[0];
                            var w = first.offsetWidth || 280;
                            var delta = w + gap;
                            var maxScroll = el.scrollWidth - el.clientWidth;
                            if (el.scrollLeft + delta >= maxScroll - 4) {
                                el.scrollTo({ left: 0, behavior: 'smooth' });
                            } else {
                                el.scrollTo({ left: el.scrollLeft + delta, behavior: 'smooth' });
                            }
                        }
                        function start() {
                            if (timer) clearInterval(timer);
                            timer = setInterval(step, intervalMs);
                        }
                        function stop() {
                            if (timer) clearInterval(timer);
                            timer = null;
                        }
                        start();
                        el.addEventListener('mouseenter', stop);
                        el.addEventListener('mouseleave', start);
                        el.addEventListener('focusin', stop);
                        el.addEventListener('focusout', start);
                    })();
                </script>
            @endpush
        @endif
    @elseif ($mode === StoreProductDisplaySetting::FEATURED_SHOWCASE)
        <div
            id="{{ $featuredShowcaseId }}"
            class="relative mt-8 overflow-hidden rounded-[1.75rem] bg-gradient-to-b from-neutral-100 via-neutral-50/80 to-white shadow-[0_2px_40px_-12px_rgba(0,0,0,0.12)] ring-1 ring-black/[0.06]"
            data-showcase
        >
            <div class="relative">
                <button
                    type="button"
                    class="showcase-prev absolute left-2 top-1/2 z-20 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full border border-neutral-200/80 bg-white/90 text-neutral-700 shadow-md backdrop-blur transition hover:bg-white md:left-4 md:h-12 md:w-12"
                    aria-controls="showcase-track"
                    aria-label="Previous featured product"
                >
                    <i class="fa-solid fa-chevron-left text-sm" aria-hidden="true"></i>
                </button>
                <button
                    type="button"
                    class="showcase-next absolute right-2 top-1/2 z-20 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full border border-neutral-200/80 bg-white/90 text-neutral-700 shadow-md backdrop-blur transition hover:bg-white md:right-4 md:h-12 md:w-12"
                    aria-controls="showcase-track"
                    aria-label="Next featured product"
                >
                    <i class="fa-solid fa-chevron-right text-sm" aria-hidden="true"></i>
                </button>

                <div class="overflow-hidden px-2 pb-2 pt-6 sm:px-6 sm:pt-10 md:px-10">
                    <div
                        id="showcase-track"
                        class="showcase-track flex transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] will-change-transform"
                        style="transform: translateX(0%)"
                    >
                        @foreach ($featuredProducts as $product)
                            @php
                                $heroImg = $product->images->first();
                                $heroUrl = $heroImg ? $heroImg->url() : '';
                            @endphp
                            <div class="w-full shrink-0 px-2 sm:px-4" data-showcase-slide>
                                <a
                                    href="{{ route('products.show', $product) }}"
                                    class="group mx-auto flex max-w-lg flex-col items-center text-center md:max-w-2xl"
                                >
                                    <div class="relative flex aspect-[4/5] w-full max-h-[min(52vh,480px)] items-center justify-center rounded-2xl bg-white/60 p-6 shadow-sm ring-1 ring-black/[0.04] md:p-10">
                                        @if ($heroUrl !== '')
                                            <img
                                                src="{{ $heroUrl }}"
                                                alt="{{ $product->name }}"
                                                class="max-h-full max-w-full object-contain transition duration-500 group-hover:scale-[1.02]"
                                                loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                                                decoding="async"
                                            >
                                        @else
                                            <span class="text-sm text-neutral-400">No image</span>
                                        @endif
                                    </div>
                                    <h3 class="mt-6 max-w-md text-balance font-semibold tracking-tight text-neutral-900 transition group-hover:text-[#0057b8] sm:text-2xl md:text-3xl">
                                        {{ $product->name }}
                                    </h3>
                                    <p class="mt-2 text-lg font-medium text-neutral-700">{{ format_ghs($product->price) }}</p>
                                    <span class="mt-4 text-sm font-medium text-[#0057b8] opacity-0 transition group-hover:opacity-100">View details <i class="fa-solid fa-arrow-right ml-1 text-xs" aria-hidden="true"></i></span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="showcase-dots flex flex-wrap items-center justify-center gap-2 px-4 pb-8 pt-2" role="tablist" aria-label="Featured products"></div>
        </div>

        @push('scripts')
            <script>
                (function () {
                    var root = document.getElementById(@json($featuredShowcaseId));
                    if (!root) return;
                    var track = root.querySelector('.showcase-track');
                    var slides = root.querySelectorAll('[data-showcase-slide]');
                    var prev = root.querySelector('.showcase-prev');
                    var next = root.querySelector('.showcase-next');
                    var dotsWrap = root.querySelector('.showcase-dots');
                    if (!track || slides.length === 0) return;

                    var i = 0;
                    var n = slides.length;
                    var autoplayMs = 8000;
                    var timer = null;

                    function syncDots() {
                        if (!dotsWrap) return;
                        dotsWrap.querySelectorAll('button').forEach(function (btn, idx) {
                            var on = idx === i;
                            btn.setAttribute('aria-selected', on ? 'true' : 'false');
                            btn.setAttribute('tabindex', on ? '0' : '-1');
                            btn.className = on
                                ? 'h-2.5 w-8 rounded-full bg-[#0057b8] transition-all focus:outline-none focus:ring-2 focus:ring-[#0057b8]/40'
                                : 'h-2.5 w-2.5 rounded-full bg-neutral-300 transition-all hover:bg-neutral-400 focus:outline-none focus:ring-2 focus:ring-[#0057b8]/40';
                        });
                    }

                    function go(to) {
                        i = ((to % n) + n) % n;
                        track.style.transform = 'translateX(' + -i * 100 + '%)';
                        syncDots();
                    }

                    function dotButtons() {
                        if (!dotsWrap || n <= 1) return;
                        dotsWrap.innerHTML = '';
                        for (var d = 0; d < n; d++) {
                            (function (idx) {
                                var b = document.createElement('button');
                                b.type = 'button';
                                b.setAttribute('role', 'tab');
                                b.setAttribute('aria-label', 'Slide ' + (idx + 1));
                                b.addEventListener('click', function () {
                                    go(idx);
                                    resetAutoplay();
                                });
                                dotsWrap.appendChild(b);
                            })(d);
                        }
                        go(0);
                    }

                    function resetAutoplay() {
                        if (timer) clearInterval(timer);
                        if (n <= 1) return;
                        timer = setInterval(function () {
                            go(i + 1);
                        }, autoplayMs);
                    }

                    if (prev) prev.addEventListener('click', function () { go(i - 1); resetAutoplay(); });
                    if (next) next.addEventListener('click', function () { go(i + 1); resetAutoplay(); });

                    if (n <= 1) {
                        if (prev) prev.style.display = 'none';
                        if (next) next.style.display = 'none';
                    }

                    dotButtons();
                    resetAutoplay();

                    root.addEventListener('mouseenter', function () { if (timer) clearInterval(timer); });
                    root.addEventListener('mouseleave', resetAutoplay);

                    var tx = null;
                    root.addEventListener('touchstart', function (e) {
                        tx = e.changedTouches[0].clientX;
                    }, { passive: true });
                    root.addEventListener('touchend', function (e) {
                        if (tx === null) return;
                        var dx = e.changedTouches[0].clientX - tx;
                        tx = null;
                        if (Math.abs(dx) < 48) return;
                        if (dx > 0) go(i - 1);
                        else go(i + 1);
                        resetAutoplay();
                    }, { passive: true });
                })();
            </script>
        @endpush
    @else
        {{-- Grid: at least 4 columns on large screens --}}
        <div class="mt-8 grid grid-cols-2 gap-5 sm:gap-6 md:grid-cols-3 lg:grid-cols-4">
            @foreach ($featuredProducts as $product)
                @include('products.partials.product-card', ['product' => $product, 'productDisplay' => $pd])
            @endforeach
        </div>
    @endif
</section>
