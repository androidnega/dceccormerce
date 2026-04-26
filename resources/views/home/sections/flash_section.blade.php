@php
    $products = $homepageSectionProducts[$section->id] ?? collect();
@endphp
@if ($products->isNotEmpty())
    <section class="border-b border-black/10 bg-[#ffeb3b] py-10 md:py-12" aria-labelledby="flash-{{ $section->id }}">
        <div class="store-box">
            <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <h2 id="flash-{{ $section->id }}" class="text-lg font-semibold tracking-tight text-neutral-900 md:text-xl">
                    {{ $section->title ?: 'Trending now' }}
                </h2>
                @if (filled($section->subtitle))
                    <p class="text-sm text-neutral-800/90">{{ $section->subtitle }}</p>
                @endif
            </div>
            <div class="relative" data-trending-carousel>
                <button
                    type="button"
                    class="trending-carousel-prev absolute left-0 top-1/2 z-20 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-slate-200/90 bg-white text-slate-700 shadow-md transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 md:-left-1 lg:h-11 lg:w-11"
                    aria-label="Previous products"
                    data-trending-prev
                >
                    <i class="fa-solid fa-chevron-left text-sm" aria-hidden="true"></i>
                </button>
                <button
                    type="button"
                    class="trending-carousel-next absolute right-0 top-1/2 z-20 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-slate-200/90 bg-white text-slate-700 shadow-md transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 md:-right-1 lg:h-11 lg:w-11"
                    aria-label="Next products"
                    data-trending-next
                >
                    <i class="fa-solid fa-chevron-right text-sm" aria-hidden="true"></i>
                </button>
                <div
                    class="store-scrollbar-none mx-8 overflow-x-auto scroll-smooth pb-1 sm:mx-10 md:mx-14"
                    data-trending-scroll
                    tabindex="0"
                >
                    <div class="flex gap-5 md:gap-6" data-trending-track>
                        @foreach ($products as $product)
                            <div class="w-[min(260px,78vw)] shrink-0 snap-start sm:w-[272px]">
                                @include('products.partials.product-card', [
                                    'product' => $product,
                                    'productDisplay' => $productDisplay,
                                    'forceGridLayout' => true,
                                    'showCategory' => false,
                                    'trendingStrip' => true,
                                ])
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    @once
        @push('scripts')
            <script>
                (function () {
                    function initTrendingCarousels() {
                        document.querySelectorAll('[data-trending-carousel]').forEach(function (root) {
                            if (root.dataset.trendingInit === '1') return;
                            root.dataset.trendingInit = '1';
                            var scroller = root.querySelector('[data-trending-scroll]');
                            var track = root.querySelector('[data-trending-track]');
                            var prev = root.querySelector('[data-trending-prev]');
                            var next = root.querySelector('[data-trending-next]');
                            if (!scroller || !track || !prev || !next) return;

                            function step() {
                                var first = track.querySelector(':scope > div');
                                if (!first) return scroller.clientWidth * 0.85;
                                var second = first.nextElementSibling;
                                if (!second) return first.offsetWidth + 20;
                                return second.offsetLeft - first.offsetLeft;
                            }

                            prev.addEventListener('click', function () {
                                scroller.scrollBy({ left: -step(), behavior: 'smooth' });
                            });
                            next.addEventListener('click', function () {
                                scroller.scrollBy({ left: step(), behavior: 'smooth' });
                            });
                        });
                    }
                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', initTrendingCarousels);
                    } else {
                        initTrendingCarousels();
                    }
                })();
            </script>
        @endpush
    @endonce
@endif
