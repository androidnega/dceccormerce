@php
    $stackedTotal = count($heroSlides);
    $stageBg = isset($homepageSettings) ? $homepageSettings->stackedCardsStageBgHex() : '#0a0a0a';
    $chromeLight = isset($homepageSettings) && $homepageSettings->stackedCardsStageChromeLight();
    $navBtnClass = $chromeLight
        ? 'border-neutral-900/12 bg-white/85 text-neutral-800 shadow-md backdrop-blur-sm transition hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900/25'
        : 'border-white/15 bg-white/10 text-white shadow-lg backdrop-blur-sm transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/70';
@endphp
@if ($stackedTotal > 0)
<section
    class="stacked-cards-hero relative overflow-hidden"
    style="background-color: {{ $stageBg }};"
    data-stacked-root
    data-stacked-chrome="{{ $chromeLight ? 'light' : 'dark' }}"
    aria-roledescription="carousel"
    aria-label="Featured products"
>
    <div class="pointer-events-none absolute inset-0 opacity-40" aria-hidden="true">
        @if ($chromeLight)
            <div class="absolute -left-24 top-0 h-80 w-80 rounded-full bg-sky-400/25 blur-3xl"></div>
            <div class="absolute -right-20 bottom-0 h-72 w-72 rounded-full bg-indigo-400/20 blur-3xl"></div>
        @else
            <div class="absolute -left-24 top-0 h-80 w-80 rounded-full bg-violet-600/30 blur-3xl"></div>
            <div class="absolute -right-20 bottom-0 h-72 w-72 rounded-full bg-blue-600/25 blur-3xl"></div>
        @endif
    </div>

    <div class="store-box relative z-10 pb-10 pt-8 sm:pb-12 sm:pt-10 lg:pb-14 lg:pt-12">
        <div class="relative mx-auto flex min-h-[min(68vh,520px)] max-w-5xl items-center justify-center sm:min-h-[min(64vh,560px)] lg:min-h-[min(62vh,600px)]">
            <button
                type="button"
                class="stacked-cards-hero__nav stacked-cards-hero__nav--prev absolute left-0 top-1/2 z-40 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full sm:h-12 sm:w-12 lg:left-2 {{ $navBtnClass }}"
                data-stacked-prev
                aria-label="Previous slide"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </button>
            <button
                type="button"
                class="stacked-cards-hero__nav stacked-cards-hero__nav--next absolute right-0 top-1/2 z-40 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full sm:h-12 sm:w-12 lg:right-2 {{ $navBtnClass }}"
                data-stacked-next
                aria-label="Next slide"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            </button>

            <div class="relative h-full w-full max-w-[min(100%,420px)] px-2 sm:max-w-[min(100%,440px)] md:max-w-[min(100%,480px)] lg:max-w-[min(100%,400px)] xl:max-w-[420px]" data-stacked-stage>
                @php
                    $stackedStart = 0;
                    $stackedPrev = $stackedTotal > 1 ? ($stackedStart - 1 + $stackedTotal) % $stackedTotal : -1;
                    $stackedNext = $stackedTotal > 1 ? ($stackedStart + 1) % $stackedTotal : -1;
                @endphp
                @foreach ($heroSlides as $i => $slide)
                    @php
                        $product = $slide['product'] ?? null;
                        $displayName = $product !== null ? $product->name : ($slide['headline'] ?? 'Featured');
                        $slideImage = $slide['image'] ?? $slide['background_image'] ?? '';
                        $ctaHref = $slide['cta_href'] ?? ($product !== null ? route('products.show', $product) : '#');
                        $tag = $slide['product_tag'] ?? null;
                        $sub = $slide['sub'] ?? '';
                        $stackedState = $i === $stackedStart ? 'is-active' : ($i === $stackedPrev ? 'is-prev' : ($i === $stackedNext ? 'is-next' : ''));
                    @endphp
                    <article
                        class="stacked-card {{ $stackedState }}"
                        data-stacked-slide="{{ $i }}"
                        aria-hidden="{{ $i === 0 ? 'false' : 'true' }}"
                    >
                        <a
                            href="{{ $ctaHref }}"
                            class="stacked-card__link group flex h-full flex-col overflow-hidden rounded-2xl bg-white shadow-[0_24px_60px_-20px_rgba(0,0,0,0.45)] ring-1 ring-black/[0.06] transition-[box-shadow] duration-300 hover:shadow-[0_28px_70px_-18px_rgba(0,0,0,0.5)] sm:rounded-3xl"
                        >
                            <div class="relative aspect-[4/3] w-full shrink-0 bg-gradient-to-b from-neutral-50 to-white">
                                @if ($tag)
                                    <span class="absolute left-3 top-3 z-10 rounded-full bg-neutral-900 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-white sm:left-4 sm:top-4 sm:px-3 sm:text-[11px]">{{ $tag }}</span>
                                @endif
                                @if ($slideImage !== '')
                                    <img
                                        src="{{ $slideImage }}"
                                        alt="{{ $slide['image_alt'] ?? $displayName }}"
                                        class="h-full w-full object-contain object-center p-4 transition duration-300 group-hover:scale-[1.02] sm:p-6"
                                        loading="{{ $i === 0 ? 'eager' : 'lazy' }}"
                                        @if ($i === 0) fetchpriority="high" @endif
                                        decoding="async"
                                    >
                                @else
                                    <div class="flex h-full items-center justify-center text-sm text-neutral-400">No image</div>
                                @endif
                            </div>
                            <div class="flex flex-1 flex-col gap-1 px-4 pb-5 pt-3 sm:px-6 sm:pb-6 sm:pt-4">
                                @if ($sub !== '')
                                    <p class="text-[11px] font-medium uppercase tracking-[0.12em] text-neutral-500 sm:text-xs">{{ $sub }}</p>
                                @endif
                                <h2 class="text-lg font-semibold leading-snug tracking-tight text-neutral-900 sm:text-xl">{{ $displayName }}</h2>
                                <span class="mt-2 inline-flex items-center text-sm font-semibold text-[#2563eb]">
                                    {{ $slide['cta_label'] ?? 'Shop now' }}
                                    <svg class="ml-1 h-4 w-4 transition group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                </span>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>

        @if ($stackedTotal > 1)
            <div class="mt-6 flex flex-wrap justify-center gap-2 sm:mt-8" role="tablist" aria-label="Slide indicators">
                @foreach ($heroSlides as $i => $_)
                    <button
                        type="button"
                        class="stacked-dot flex h-10 min-h-[44px] min-w-[44px] touch-manipulation items-center justify-center rounded-full outline-none transition active:opacity-70"
                        data-stacked-dot="{{ $i }}"
                        aria-label="Go to slide {{ $i + 1 }}"
                        aria-current="{{ $i === 0 ? 'true' : 'false' }}"
                    >
                        <span
                            class="stacked-dot-pill block h-2 rounded-full transition-all {{ $i === 0 ? ($chromeLight ? 'w-6 bg-neutral-900' : 'w-6 bg-white') : ($chromeLight ? 'w-2 bg-neutral-900/35' : 'w-2 bg-white/35') }}"
                            aria-hidden="true"
                        ></span>
                    </button>
                @endforeach
            </div>
        @endif
    </div>
</section>

<style>
    .stacked-cards-hero .stacked-card {
        position: absolute;
        left: 50%;
        top: 50%;
        width: 100%;
        max-width: 100%;
        transform: translate(-50%, -50%) translateX(0) scale(0.8);
        opacity: 0;
        z-index: 5;
        pointer-events: none;
        transition:
            transform 0.45s cubic-bezier(0.22, 1, 0.36, 1),
            opacity 0.45s cubic-bezier(0.22, 1, 0.36, 1),
            z-index 0s linear 0.45s;
    }

    .stacked-cards-hero .stacked-card.is-active {
        transform: translate(-50%, -50%) translateX(0) scale(1);
        opacity: 1;
        z-index: 30;
        pointer-events: auto;
        transition:
            transform 0.45s cubic-bezier(0.22, 1, 0.36, 1),
            opacity 0.45s cubic-bezier(0.22, 1, 0.36, 1),
            z-index 0s;
    }

    .stacked-cards-hero .stacked-card.is-prev {
        transform: translate(-50%, -50%) translateX(calc(-1 * min(34vw, 9.5rem))) scale(0.9);
        opacity: 0.6;
        z-index: 20;
        pointer-events: none;
    }

    .stacked-cards-hero .stacked-card.is-next {
        transform: translate(-50%, -50%) translateX(min(34vw, 9.5rem)) scale(0.9);
        opacity: 0.6;
        z-index: 20;
        pointer-events: none;
    }

    /* Tablet: tighter peek */
    @media (min-width: 768px) and (max-width: 1023px) {
        .stacked-cards-hero .stacked-card.is-prev {
            transform: translate(-50%, -50%) translateX(calc(-1 * min(30vw, 11rem))) scale(0.88);
            opacity: 0.55;
        }
        .stacked-cards-hero .stacked-card.is-next {
            transform: translate(-50%, -50%) translateX(min(30vw, 11rem)) scale(0.88);
            opacity: 0.55;
        }
    }

    /* Desktop: full stacked overlap */
    @media (min-width: 1024px) {
        .stacked-cards-hero .stacked-card.is-prev {
            transform: translate(-50%, -50%) translateX(calc(-1 * min(28vw, 13.5rem))) scale(0.9);
            opacity: 0.6;
        }
        .stacked-cards-hero .stacked-card.is-next {
            transform: translate(-50%, -50%) translateX(min(28vw, 13.5rem)) scale(0.9);
            opacity: 0.6;
        }
    }

    /* Mobile: single visible card */
    @media (max-width: 767px) {
        .stacked-cards-hero .stacked-card.is-prev,
        .stacked-cards-hero .stacked-card.is-next {
            opacity: 0 !important;
            transform: translate(-50%, -50%) scale(0.82) !important;
            z-index: 0 !important;
            pointer-events: none !important;
            transition: opacity 0.35s ease, transform 0.35s ease;
        }
    }

    .stacked-cards-hero[data-reduced-motion="1"] .stacked-card {
        transition-duration: 0.01ms;
    }
</style>

@push('scripts')
    <script>
        (function () {
            var root = document.querySelector('[data-stacked-root]');
            if (!root) return;
            var stage = root.querySelector('[data-stacked-stage]');
            var slides = root.querySelectorAll('[data-stacked-slide]');
            var dots = root.querySelectorAll('[data-stacked-dot]');
            var prevBtn = root.querySelector('[data-stacked-prev]');
            var nextBtn = root.querySelector('[data-stacked-next]');
            if (!stage || !slides.length) return;

            var total = slides.length;
            var idx = 0;
            var autoplayMs = 4500;
            var autoplayTimer = null;
            var chromeLight = root.getAttribute('data-stacked-chrome') === 'light';
            var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (reduceMotion) root.setAttribute('data-reduced-motion', '1');

            function dotPillClasses(on) {
                var base = 'stacked-dot-pill block h-2 rounded-full transition-all ';
                if (chromeLight) {
                    return base + (on ? 'w-6 bg-neutral-900' : 'w-2 bg-neutral-900/35');
                }
                return base + (on ? 'w-6 bg-white' : 'w-2 bg-white/35');
            }

            function mod(n, m) {
                return ((n % m) + m) % m;
            }

            function roleFor(j) {
                if (j === idx) return 'active';
                if (j === mod(idx - 1, total)) return 'prev';
                if (j === mod(idx + 1, total)) return 'next';
                return 'hidden';
            }

            function paint() {
                slides.forEach(function (el, j) {
                    var r = roleFor(j);
                    el.classList.remove('is-active', 'is-prev', 'is-next');
                    if (r === 'active') {
                        el.classList.add('is-active');
                        el.setAttribute('aria-hidden', 'false');
                    } else {
                        el.setAttribute('aria-hidden', 'true');
                        if (r === 'prev') el.classList.add('is-prev');
                        else if (r === 'next') el.classList.add('is-next');
                    }
                });
                dots.forEach(function (d, j) {
                    var on = j === idx;
                    d.setAttribute('aria-current', on ? 'true' : 'false');
                    var pill = d.querySelector('.stacked-dot-pill');
                    if (pill) pill.className = dotPillClasses(on);
                });
            }

            function go(i) {
                idx = mod(i, total);
                paint();
            }

            function startAutoplay() {
                if (reduceMotion || total < 2) return;
                stopAutoplay();
                autoplayTimer = window.setInterval(function () {
                    if (!document.hidden) go(idx + 1);
                }, autoplayMs);
            }

            function stopAutoplay() {
                if (autoplayTimer) {
                    clearInterval(autoplayTimer);
                    autoplayTimer = null;
                }
            }

            if (prevBtn) prevBtn.addEventListener('click', function () { stopAutoplay(); go(idx - 1); startAutoplay(); });
            if (nextBtn) nextBtn.addEventListener('click', function () { stopAutoplay(); go(idx + 1); startAutoplay(); });
            dots.forEach(function (d, j) {
                d.addEventListener('click', function () { stopAutoplay(); go(j); startAutoplay(); });
            });

            root.addEventListener('mouseenter', stopAutoplay);
            root.addEventListener('mouseleave', startAutoplay);

            document.addEventListener('visibilitychange', function () {
                if (document.hidden) stopAutoplay();
                else startAutoplay();
            });

            var touchStartX = 0;
            var touchActive = false;
            stage.addEventListener('touchstart', function (e) {
                if (e.touches.length !== 1) return;
                touchActive = true;
                touchStartX = e.touches[0].clientX;
            }, { passive: true });
            stage.addEventListener('touchend', function (e) {
                if (!touchActive || e.changedTouches.length !== 1) {
                    touchActive = false;
                    return;
                }
                var dx = e.changedTouches[0].clientX - touchStartX;
                touchActive = false;
                if (Math.abs(dx) < 48) return;
                stopAutoplay();
                if (dx < 0) go(idx + 1);
                else go(idx - 1);
                startAutoplay();
            }, { passive: true });

            paint();
            startAutoplay();
        })();
    </script>
@endpush
@endif
