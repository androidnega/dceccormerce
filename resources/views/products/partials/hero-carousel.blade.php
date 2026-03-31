@php
    $heroCount = count($heroSlides);
    $isSidebar = ($heroVariant ?? 'default') === 'sidebar';
    if ($isSidebar) {
        $heroBg = 'bg-primary-50/90';
        $heroSectionStyle = '';
        $fwNavBtnClass = '';
        $fwBlobA = '';
        $fwBlobB = '';
        $fwBlobC = '';
        $fwText = '';
        $fwSub = '';
        $fwMuted = '';
        $fwDotIdle = '';
    } else {
        $heroBg = 'relative overflow-hidden';
        $fwHs = $homepageSettings ?? \App\Models\HomepageSetting::current();
        $fwBg = $fwHs->heroFullwidthBgHex();
        $fwBorder = $fwHs->heroFullwidthBorderHex();
        $fwText = $fwHs->heroFullwidthTextHex();
        $fwSub = $fwHs->heroFullwidthSubColor();
        $fwMuted = $fwHs->heroFullwidthMutedColor();
        $fwDotIdle = $fwHs->heroFullwidthDotIdleRgba();
        $fwBgLight = $fwHs->heroFullwidthBgIsLight();
        $heroSectionStyle = 'background-color:'.$fwBg.';border-bottom:1px solid '.$fwBorder.';--hero-fw-dot-active:'.$fwText.';--hero-fw-dot-idle:'.$fwDotIdle.';';
        $fwNavBtnClass = $fwBgLight
            ? 'border-[#c5d5ea] bg-white/90 text-[#334155] shadow-sm backdrop-blur-sm transition hover:border-[#0057b8]/30 hover:text-[#0057b8] active:scale-95 focus-visible:ring-2 focus-visible:ring-indigo-500/30'
            : 'border-white/15 bg-white/10 text-white shadow-lg backdrop-blur-sm transition hover:bg-white/20 active:scale-95 focus-visible:ring-2 focus-visible:ring-white/70';
        $fwBlobA = \App\Models\HomepageSetting::rgbaFromHex($fwText, 0.07);
        $fwBlobB = \App\Models\HomepageSetting::rgbaFromHex('#0057b8', $fwBgLight ? 0.1 : 0.14);
        $fwBlobC = $fwBgLight ? 'rgba(255,255,255,0.45)' : 'rgba(255,255,255,0.12)';
    }
@endphp
<style>
    .hero-viewport {
        contain: paint;
        -webkit-overflow-scrolling: touch;
    }
    @keyframes hero-content-reveal {
        from {
            opacity: 0;
            transform: translate3d(1.25rem, 0.5rem, 0);
        }
        to {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
    }
    @keyframes hero-visual-reveal {
        from {
            opacity: 0;
            transform: translate3d(-1rem, 0.35rem, 0);
        }
        to {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
    }
    .hero-panel.is-active .hero-line-sub {
        animation: hero-content-reveal 0.55s cubic-bezier(0.22, 1, 0.36, 1) 0.05s both;
    }
    .hero-panel.is-active .hero-line-title {
        animation: hero-content-reveal 0.65s cubic-bezier(0.22, 1, 0.36, 1) 0.14s both;
    }
    .hero-panel.is-active .hero-line-cta {
        animation: hero-content-reveal 0.55s cubic-bezier(0.22, 1, 0.36, 1) 0.28s both;
    }
    .hero-panel.is-active .hero-visual {
        animation: hero-visual-reveal 0.65s cubic-bezier(0.22, 1, 0.36, 1) 0.08s both;
    }
    .hero-panel:not(.is-active) .hero-line-sub,
    .hero-panel:not(.is-active) .hero-line-title,
    .hero-panel:not(.is-active) .hero-line-cta,
    .hero-panel:not(.is-active) .hero-visual {
        opacity: 0;
    }
</style>
<section
    @class([
        'hero-carousel',
        'group',
        $heroBg,
        'flex min-h-0 w-full flex-1 flex-col pb-2 sm:pb-4 lg:pb-10 lg:h-full' => $isSidebar,
    ])
    aria-label="Featured products"
    data-hero-total="{{ $heroCount }}"
    @if ($isSidebar) data-hero-sidebar="1" @endif
    @if (! $isSidebar && $heroSectionStyle !== '') style="{{ $heroSectionStyle }}" @endif
>
    @if (! $isSidebar)
        <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
            <div class="absolute -left-24 top-0 h-80 w-80 rounded-full blur-3xl motion-safe:animate-pulse" style="background-color: {{ $fwBlobA }}"></div>
            <div class="absolute -right-20 bottom-0 h-72 w-72 rounded-full blur-3xl motion-safe:animate-pulse" style="animation-delay: 1.2s;background-color: {{ $fwBlobB }}"></div>
            <div class="absolute left-[42%] top-1/2 h-[28rem] w-[28rem] -translate-x-1/2 -translate-y-1/2 rounded-full blur-3xl" style="background-color: {{ $fwBlobC }}"></div>
        </div>
    @endif
    @if ($isSidebar)
    <div @class([
        'pointer-events-none absolute inset-y-0 left-0 z-30 hidden items-center justify-center lg:flex opacity-0 transition-opacity group-hover:opacity-100 group-focus-within:opacity-100 group-hover:pointer-events-auto group-focus-within:pointer-events-auto',
        'w-11 sm:w-14 lg:w-16',
    ])>
        <button
            type="button"
            @class([
                'pointer-events-auto flex h-11 w-11 shrink-0 touch-manipulation items-center justify-center rounded-full border border-primary-100 bg-white/95 text-neutral-600 shadow-[0_2px_12px_rgba(234,179,8,0.12)] backdrop-blur-sm transition hover:text-primary-800 active:scale-95',
            ])
            data-hero-prev
            aria-label="Previous slide"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
        </button>
    </div>
    <div @class([
        'pointer-events-none absolute inset-y-0 right-0 z-30 hidden items-center justify-center lg:flex opacity-0 transition-opacity group-hover:opacity-100 group-focus-within:opacity-100 group-hover:pointer-events-auto group-focus-within:pointer-events-auto',
        'w-11 sm:w-14 lg:w-16',
    ])>
        <button
            type="button"
            @class([
                'pointer-events-auto flex h-11 w-11 shrink-0 touch-manipulation items-center justify-center rounded-full border border-primary-100 bg-white/95 text-neutral-600 shadow-[0_2px_12px_rgba(234,179,8,0.12)] backdrop-blur-sm transition hover:text-primary-800 active:scale-95',
            ])
            data-hero-next
            aria-label="Next slide"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        </button>
    </div>
    @else
        <div class="store-box relative z-10">
        <div class="pointer-events-none absolute inset-y-0 left-0 z-30 hidden w-10 items-center justify-center sm:w-12 lg:flex opacity-0 transition-opacity group-hover:opacity-100 group-focus-within:opacity-100 group-hover:pointer-events-auto group-focus-within:pointer-events-auto">
            <button
                type="button"
                class="pointer-events-auto flex h-11 w-11 shrink-0 touch-manipulation items-center justify-center rounded-full border focus:outline-none focus-visible:ring-2 {{ $fwNavBtnClass }}"
                data-hero-prev
                aria-label="Previous slide"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </button>
        </div>
        <div class="pointer-events-none absolute inset-y-0 right-0 z-30 hidden w-10 items-center justify-center sm:w-12 lg:flex opacity-0 transition-opacity group-hover:opacity-100 group-focus-within:opacity-100 group-hover:pointer-events-auto group-focus-within:pointer-events-auto">
            <button
                type="button"
                class="pointer-events-auto flex h-11 w-11 shrink-0 touch-manipulation items-center justify-center rounded-full border focus:outline-none focus-visible:ring-2 {{ $fwNavBtnClass }}"
                data-hero-next
                aria-label="Next slide"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            </button>
        </div>
        <div class="relative w-full px-3 sm:px-5 lg:pl-8 lg:pr-8 xl:pl-10 xl:pr-10">
    @endif

    @if ($isSidebar)
    <div class="flex min-h-0 w-full flex-1 flex-col px-3 sm:px-6 lg:px-12">
    @endif
        <div
            @class([
                'hero-viewport relative w-full overflow-hidden touch-pan-y',
                'min-h-0 flex-1' => $isSidebar,
            ])
            data-hero-viewport
        >
            <div
                @class([
                    'hero-track flex flex-nowrap transition-transform duration-700 ease-[cubic-bezier(0.25,0.1,0.25,1)] will-change-transform',
                    'h-full min-h-0' => $isSidebar,
                ])
                data-hero-track
                style="transform: translate3d(0, 0, 0);"
            >
                @foreach ($heroSlides as $i => $slide)
                    @php
                        $ctaHref = $slide['cta_href'] ?? (isset($slide['product']) && $slide['product'] ? route('products.show', $slide['product']) : '#');
                        $ctaLabel = $slide['cta_label'] ?? 'Shop now';
                        $imgAlt = $slide['image_alt'] ?? (isset($slide['product']) && $slide['product'] ? $slide['product']->name : ($slide['headline'] ?? 'Hero'));
                        $slideImage = $slide['image'] ?? $slide['background_image'] ?? '';
                    @endphp
                    <div
                        @class([
                            'hero-panel box-border flex shrink-0 items-center',
                            'overflow-visible' => $isSidebar,
                            'overflow-hidden' => ! $isSidebar,
                            'h-full min-h-0 items-stretch py-2 sm:py-4 lg:py-8' => $isSidebar,
                            'min-h-[300px] py-6 sm:min-h-[340px] sm:py-8 lg:min-h-[380px] lg:py-10' => ! $isSidebar,
                            $i === 0 ? 'is-active' : '',
                        ])
                        data-hero-slide="{{ $i }}"
                        aria-hidden="{{ $i === 0 ? 'false' : 'true' }}"
                    >
                        @if ($isSidebar)
                            @php
                                $splitTitleClass = 'hero-line-title mt-1.5 text-[clamp(0.8125rem,3.4vw,1.0625rem)] font-bold leading-[1.18] tracking-[-0.02em] text-neutral-900 sm:mt-2 sm:text-base md:text-lg lg:mt-3 lg:text-2xl lg:leading-[1.1] xl:text-[2.5rem] xl:leading-[1.08]';
                                $splitSubClass = 'hero-line-sub text-[11px] font-medium leading-snug text-neutral-600 sm:text-xs lg:text-sm';
                                $splitCtaClass = 'hero-line-cta mt-2.5 inline-flex w-full min-w-0 max-w-full items-center justify-center rounded-md bg-[#2563eb] px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.14em] text-white transition hover:bg-[#1d4ed8] sm:mt-3.5 sm:px-4 sm:py-2.5 sm:text-[11px] sm:tracking-[0.16em] md:mt-4 md:px-5 md:text-xs lg:mt-8 lg:px-10 lg:tracking-[0.2em]';
                            @endphp
                            <div class="mx-auto grid min-h-0 w-full max-w-[1280px] grid-cols-[minmax(0,1fr)_minmax(0,1.08fr)] items-center gap-x-2 overflow-hidden px-0 sm:gap-x-3 sm:px-1 md:gap-x-4 lg:gap-x-6 xl:gap-x-10">
                                <div class="hero-text-block order-1 flex min-h-0 min-w-0 flex-col items-start justify-center self-stretch text-left lg:max-w-[min(100%,28rem)]">
                                    <p @class([$splitSubClass])>{{ $slide['sub'] }}</p>
                                    <h2 @class([$splitTitleClass])>
                                        @if (! empty($slide['headline_lines'] ?? null))
                                            @foreach ($slide['headline_lines'] as $line)
                                                <span class="block break-words [overflow-wrap:anywhere]">{{ $line }}</span>
                                            @endforeach
                                        @else
                                            <span class="break-words [overflow-wrap:anywhere]">{{ $slide['headline'] }}</span>
                                        @endif
                                    </h2>
                                    <a href="{{ $ctaHref }}" @class([$splitCtaClass])>{{ $ctaLabel }}</a>
                                </div>
                                <div class="hero-visual order-2 flex min-h-0 min-w-0 shrink-0 items-center justify-end overflow-visible">
                                    @if ($slideImage !== '')
                                        <img
                                            src="{{ $slideImage }}"
                                            alt="{{ $imgAlt }}"
                                            class="h-auto w-full max-w-full object-contain object-center sm:max-w-lg"
                                            loading="{{ $i === 0 ? 'eager' : 'lazy' }}"
                                            @if ($i === 0) fetchpriority="high" @endif
                                        >
                                    @else
                                        <div class="flex h-16 w-full max-w-lg items-center justify-center rounded-2xl bg-neutral-200/80 text-[10px] text-neutral-400 sm:h-28 sm:text-xs">No image</div>
                                    @endif
                                </div>
                            </div>
                        @else
                            @php
                                $splitTitleClass = 'hero-line-title mt-1.5 text-2xl font-bold leading-[1.08] tracking-[-0.02em] sm:mt-2 sm:text-3xl sm:leading-[1.06] md:text-4xl md:leading-[1.05] lg:mt-2 lg:text-5xl lg:leading-[1.02] xl:text-[3.15rem] 2xl:text-[3.45rem]';
                                $splitSubClass = 'hero-line-sub text-sm font-medium leading-snug sm:text-base';
                                $splitCtaClass = 'hero-line-cta mt-5 inline-flex w-full min-w-0 items-center justify-center rounded-lg bg-[#0057b8] px-6 py-3.5 text-[11px] font-semibold uppercase tracking-[0.15em] text-white shadow-sm transition hover:bg-[#00479a] sm:mt-6 sm:w-auto sm:min-w-[176px] sm:px-8 sm:text-xs sm:tracking-[0.17em] lg:mt-8 lg:py-4 lg:text-[13px]';
                            @endphp
                            <div class="mx-auto grid w-full max-w-[1180px] grid-cols-1 items-center gap-5 sm:gap-6 lg:grid-cols-2 lg:gap-7 xl:gap-8">
                                <div class="hero-text-block order-1 flex min-w-0 flex-col items-stretch pr-0 text-left sm:items-start lg:max-w-none lg:pr-1">
                                    <p @class([$splitSubClass]) style="color: {{ $fwSub }}">{{ $slide['sub'] }}</p>
                                    <h2 @class([$splitTitleClass]) style="color: {{ $fwText }}">
                                        @if (! empty($slide['headline_lines'] ?? null))
                                            @foreach ($slide['headline_lines'] as $line)
                                                <span class="block break-words [overflow-wrap:anywhere]">{{ $line }}</span>
                                            @endforeach
                                        @else
                                            <span class="break-words [overflow-wrap:anywhere]">{{ $slide['headline'] }}</span>
                                        @endif
                                    </h2>
                                    <a href="{{ $ctaHref }}" @class([$splitCtaClass])>{{ $ctaLabel }}</a>
                                </div>
                                <div class="hero-visual order-2 flex min-h-0 w-full min-w-0 items-center justify-center lg:justify-end lg:pl-0">
                                    @if ($slideImage !== '')
                                        <img
                                            src="{{ $slideImage }}"
                                            alt="{{ $imgAlt }}"
                                            class="h-auto w-full max-w-[min(100%,400px)] object-contain object-center sm:max-w-[min(100%,440px)] md:max-w-[min(100%,480px)] lg:ml-0 lg:max-w-[min(100%,540px)] lg:object-right xl:max-w-[min(100%,580px)] max-h-[min(220px,38vh)] sm:max-h-[min(280px,42vh)] md:max-h-[min(320px,44vh)] lg:max-h-[min(400px,52vh)] xl:max-h-[min(440px,54vh)] 2xl:max-h-[min(460px,56vh)]"
                                            loading="{{ $i === 0 ? 'eager' : 'lazy' }}"
                                            @if ($i === 0) fetchpriority="high" @endif
                                            decoding="async"
                                        >
                                    @else
                                        <div class="flex h-44 w-full max-w-md items-center justify-center text-sm sm:h-56" style="color: {{ $fwMuted }}">No image</div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @if ($isSidebar)
    </div>
    @else
        </div>
    </div>
    @endif

    <div @class([
        $isSidebar ? 'absolute bottom-0 left-0 right-0 z-20 flex flex-wrap justify-center gap-1 pb-2 sm:gap-2 sm:pb-4 lg:pb-5' : 'store-box relative z-20 flex flex-wrap justify-center gap-1 pb-6 sm:gap-2 sm:pb-8',
    ])>
        @foreach ($heroSlides as $i => $_)
            <button
                type="button"
                class="hero-dot flex h-11 min-h-[44px] min-w-[44px] touch-manipulation items-center justify-center rounded-full outline-none transition active:opacity-70"
                data-hero-dot="{{ $i }}"
                aria-label="Go to slide {{ $i + 1 }}"
                aria-current="{{ $i === 0 ? 'true' : 'false' }}"
            >
                <span
                    class="hero-dot-pill block h-2 rounded-full transition-all {{ $isSidebar ? ($i === 0 ? 'w-6 bg-[#2563eb]' : 'w-2 bg-neutral-300') : ($i === 0 ? 'w-6' : 'w-2') }}"
                    @if (! $isSidebar)
                        style="background-color: {{ $i === 0 ? $fwText : $fwDotIdle }}"
                    @endif
                    aria-hidden="true"
                ></span>
            </button>
        @endforeach
    </div>
</section>

@push('scripts')
    <script>
        (function () {
            var viewport = document.querySelector('[data-hero-viewport]');
            var track = document.querySelector('[data-hero-track]');
            var panels = document.querySelectorAll('[data-hero-slide]');
            var dots = document.querySelectorAll('[data-hero-dot]');
            var prev = document.querySelector('[data-hero-prev]');
            var next = document.querySelector('[data-hero-next]');
            if (!track || !viewport || !panels.length) return;
            var total = panels.length;
            var idx = 0;
            var slideMs = 700;
            var locked = false;
            var lockTimer = null;
            var entranceTimer = null;
            var touchStartX = 0;
            var touchStartY = 0;
            var touchActive = false;
            var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            var autoplayTimer = null;

            function slideWidth() {
                return Math.max(0, Math.round(viewport.getBoundingClientRect().width));
            }

            function layout() {
                var w = slideWidth();
                if (w < 1) return;
                track.style.width = total * w + 'px';
                var sidebarRoot = document.querySelector('.hero-carousel[data-hero-sidebar="1"]');
                var h = 0;
                if (sidebarRoot && viewport) {
                    h = Math.round(viewport.getBoundingClientRect().height);
                }
                for (var i = 0; i < panels.length; i++) {
                    panels[i].style.flex = '0 0 ' + w + 'px';
                    panels[i].style.width = w + 'px';
                    panels[i].style.maxWidth = w + 'px';
                    panels[i].style.minWidth = w + 'px';
                    if (h > 0) {
                        panels[i].style.minHeight = h + 'px';
                    } else {
                        panels[i].style.minHeight = '';
                    }
                }
            }

            function setTransform() {
                var w = slideWidth();
                if (w < 1) return;
                var x = Math.round(idx * w);
                track.style.transform = 'translate3d(-' + x + 'px, 0, 0)';
            }

            function triggerPanelEntrance() {
                void track.offsetWidth;
                requestAnimationFrame(function () {
                    requestAnimationFrame(function () {
                        if (panels[idx]) {
                            panels[idx].classList.add('is-active');
                        }
                    });
                });
            }

            function setNavDisabled(disabled) {
                if (prev) prev.disabled = disabled;
                if (next) next.disabled = disabled;
                dots.forEach(function (d) {
                    d.disabled = disabled;
                });
            }

            function show(i) {
                if (locked) return;
                var nextIdx = (i + total) % total;
                if (nextIdx === idx) return;
                if (entranceTimer) clearTimeout(entranceTimer);
                panels.forEach(function (p) {
                    p.classList.remove('is-active');
                });
                idx = nextIdx;
                locked = true;
                setNavDisabled(true);
                if (lockTimer) clearTimeout(lockTimer);
                lockTimer = setTimeout(function () {
                    locked = false;
                    setNavDisabled(false);
                    lockTimer = null;
                }, slideMs);
                setTransform();
                panels.forEach(function (p, j) {
                    p.setAttribute('aria-hidden', j === idx ? 'false' : 'true');
                });
                var heroRoot = viewport.closest('.hero-carousel');
                var heroCs = heroRoot ? window.getComputedStyle(heroRoot) : null;
                var fwDotActive = heroCs ? heroCs.getPropertyValue('--hero-fw-dot-active').trim() : '';
                var fwDotIdle = heroCs ? heroCs.getPropertyValue('--hero-fw-dot-idle').trim() : '';
                var useFwDots = fwDotActive !== '';
                dots.forEach(function (d, j) {
                    var on = j === idx;
                    d.setAttribute('aria-current', on ? 'true' : 'false');
                    var pill = d.querySelector('.hero-dot-pill');
                    if (!pill) return;
                    if (useFwDots) {
                        pill.style.width = on ? '1.5rem' : '0.5rem';
                        pill.style.backgroundColor = on ? fwDotActive : (fwDotIdle || 'rgba(0,0,0,0.2)');
                        pill.classList.remove('bg-[#2563eb]', 'bg-neutral-300');
                    } else {
                        pill.style.width = '';
                        pill.style.backgroundColor = '';
                        pill.classList.toggle('w-6', on);
                        pill.classList.toggle('w-2', !on);
                        pill.classList.toggle('bg-[#2563eb]', on);
                        pill.classList.toggle('bg-neutral-300', !on);
                    }
                });
                entranceTimer = setTimeout(function () {
                    entranceTimer = null;
                    triggerPanelEntrance();
                }, slideMs);
            }

            if (prev) prev.addEventListener('click', function () { show(idx - 1); });
            if (next) next.addEventListener('click', function () { show(idx + 1); });
            dots.forEach(function (d, j) {
                d.addEventListener('click', function () { show(j); });
            });

            function startAutoplay() {
                if (reduceMotion || total < 2) return;
                stopAutoplay();
                autoplayTimer = window.setInterval(function () {
                    if (!document.hidden && !locked) show(idx + 1);
                }, 7000);
            }

            function stopAutoplay() {
                if (autoplayTimer) {
                    clearInterval(autoplayTimer);
                    autoplayTimer = null;
                }
            }

            document.addEventListener('visibilitychange', function () {
                if (document.hidden) stopAutoplay();
                else startAutoplay();
            });

            viewport.addEventListener('touchstart', function (e) {
                if (e.touches.length !== 1) return;
                touchActive = true;
                touchStartX = e.touches[0].clientX;
                touchStartY = e.touches[0].clientY;
            }, { passive: true });

            viewport.addEventListener('touchend', function (e) {
                if (!touchActive || e.changedTouches.length !== 1) {
                    touchActive = false;
                    return;
                }
                var endX = e.changedTouches[0].clientX;
                var endY = e.changedTouches[0].clientY;
                var dx = endX - touchStartX;
                var dy = endY - touchStartY;
                touchActive = false;
                if (Math.abs(dx) < 56 || Math.abs(dx) < Math.abs(dy)) return;
                if (dx < 0) show(idx + 1);
                else show(idx - 1);
            }, { passive: true });

            startAutoplay();

            function onResize() {
                layout();
                setTransform();
            }

            if (typeof ResizeObserver !== 'undefined') {
                var ro = new ResizeObserver(function () {
                    window.requestAnimationFrame(onResize);
                });
                ro.observe(viewport);
            } else {
                window.addEventListener('resize', function () {
                    window.requestAnimationFrame(onResize);
                });
            }

            var bootAttempts = 0;

            function boot() {
                layout();
                setTransform();
                if (slideWidth() < 1 && bootAttempts < 90) {
                    bootAttempts += 1;
                    window.requestAnimationFrame(boot);
                }
            }

            boot();
        })();
    </script>
@endpush
