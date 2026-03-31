@extends('layout')

@section('title', 'Store — ' . config('app.name'))

@section('main_class', 'w-full flex-1')

@section('content')
    @if (isset($useStackedCardsLayout) && $useStackedCardsLayout && $showHighlights && count($heroSlides) > 0)
        @include('home.sliders.stacked_cards', ['heroSlides' => $heroSlides])
    @elseif (isset($useSidebarHomeLayout) && $useSidebarHomeLayout && $showHighlights && count($heroSlides) > 0)
        <div class="store-box pb-6 pt-4">
            <div class="relative w-full lg:min-h-[min(100%,520px)]">
                <aside class="relative z-10 w-full shrink-0 overflow-hidden rounded-sm border border-neutral-300 bg-white shadow-[0_1px_3px_rgba(0,0,0,0.06)] lg:w-[260px]">
                    <div class="flex shrink-0 items-center justify-between bg-[#1a2f4a] px-4 py-3 text-white sm:px-5 sm:py-3.5">
                        <span class="text-[11px] font-semibold uppercase tracking-[0.2em]">Categories</span>
                        <button type="button" class="hidden p-0.5 text-white/90 lg:inline-flex lg:pointer-events-none" aria-label="Menu" tabindex="-1">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                        </button>
                    </div>
                    {{-- Mobile / tablet: compact horizontal strip (not a long list) --}}
                    <div class="lg:hidden border-b border-neutral-100 bg-[#f8fafc] px-2 py-2.5">
                        <div class="home-cat-scroll flex gap-2 overflow-x-auto overscroll-x-contain px-1 pb-1 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden snap-x snap-mandatory">
                            @foreach ($sidebarCategories->filter(fn ($c) => $c !== null) as $cat)
                                <a
                                    href="{{ route('shop.category', $cat) }}"
                                    class="snap-start shrink-0 inline-flex max-w-[11rem] items-center gap-2 rounded-full border border-neutral-200/90 bg-white py-2 pl-2 pr-3.5 text-left text-[12px] font-medium leading-tight text-neutral-800 shadow-sm ring-1 ring-black/[0.03] transition hover:border-[#1a2f4a]/40 hover:bg-white"
                                >
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-neutral-100 text-[12px] text-[#1a2f4a]" aria-hidden="true"><i class="{{ category_fa_classes($cat->slug, $cat->name) }}"></i></span>
                                    <span class="line-clamp-2">{{ $cat->name }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    {{-- Desktop: full vertical list --}}
                    <ul class="hidden divide-y divide-neutral-200 lg:block">
                        @foreach ($sidebarCategories->filter(fn ($c) => $c !== null) as $cat)
                            <li>
                                <a href="{{ route('shop.category', $cat) }}" class="flex items-center gap-3 px-5 py-2.5 text-[13px] leading-snug text-neutral-600 transition hover:bg-[#ffeb3b]/30 hover:text-neutral-900">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-neutral-100 text-[13px] text-[#1a2f4a]" aria-hidden="true"><i class="{{ category_fa_classes($cat->slug, $cat->name) }}"></i></span>
                                    <span>{{ $cat->name }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </aside>
                {{-- Mobile: fixed aspect slideshow; desktop: fills column beside categories --}}
                <div class="relative mt-3 flex aspect-[16/9] min-h-0 min-w-0 flex-col overflow-hidden rounded-sm border border-neutral-300 bg-white shadow-[0_1px_3px_rgba(0,0,0,0.06)] sm:mt-4 sm:aspect-[16/10] lg:absolute lg:inset-y-0 lg:left-[calc(260px+1rem)] lg:right-0 lg:mt-0 lg:aspect-auto lg:min-h-0 lg:h-full">
                    @include('products.partials.hero-carousel', ['heroVariant' => 'sidebar', 'homepageSettings' => $homepageSettings])
                </div>
            </div>
        </div>
    @elseif ($showHighlights && count($heroSlides) > 0)
        @include('products.partials.hero-carousel', ['homepageSettings' => $homepageSettings])
    @endif

    @if ($showHighlights && isset($saleSpotlightItems) && is_array($saleSpotlightItems) && count($saleSpotlightItems) > 0)
        @include('products.partials.home-sale-spotlight', ['spotlightItems' => $saleSpotlightItems])
    @endif

    @if ($showHighlights)
        @php
            $homepageSectionTypes = \App\Models\HomepageSection::TYPES;
        @endphp
        @foreach ($homepageSections as $section)
            @if ($section->type === \App\Models\HomepageSection::TYPE_CATEGORY_BLOCK)
                @if ($section->is_active && \App\Models\CategoryBanner::query()->activeOrdered()->limit(3)->exists())
                    @include('home.sections.category_block', [
                        'section' => $section,
                        'productDisplay' => $productDisplay,
                        'homepageSectionProducts' => $homepageSectionProducts ?? [],
                    ])
                @endif
                @continue
            @endif
            @if (in_array($section->type, $homepageSectionTypes, true) && view()->exists($v = 'home.sections.'.$section->type))
                @include($v, [
                    'section' => $section,
                    'productDisplay' => $productDisplay,
                    'homepageSectionProducts' => $homepageSectionProducts ?? [],
                ])
            @endif
        @endforeach

        {{-- Popular news --}}
        <section id="popular-news" class="border-t border-neutral-100 bg-[#fafafa] py-12 sm:py-14" aria-labelledby="news-heading">
            <div class="store-box">
                <h2 id="news-heading" class="text-center text-2xl font-bold tracking-tight text-neutral-900">Popular news</h2>
                <p class="mx-auto mt-2 max-w-2xl text-center text-sm text-neutral-500">Stories, tips, and updates from the store.</p>
                <div class="mt-10 grid gap-6 sm:grid-cols-2 md:gap-7 lg:grid-cols-3 lg:items-stretch">
                    @forelse ($newsPosts as $post)
                        @php
                            $newsImg = $post->resolveImageUrl();
                        @endphp
                        <article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-neutral-200/90 bg-white shadow-[0_1px_2px_rgba(0,0,0,0.04)] ring-1 ring-black/[0.03] transition hover:border-neutral-300 hover:shadow-md hover:ring-black/[0.05]">
                            <a href="{{ $post->resolveLinkUrl() }}" class="relative block aspect-[4/3] w-full shrink-0 overflow-hidden bg-neutral-100">
                                @if ($newsImg !== '')
                                    <img
                                        src="{{ $newsImg }}"
                                        alt=""
                                        class="absolute inset-0 h-full w-full object-cover object-center transition duration-300 group-hover:brightness-[0.97]"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                @else
                                    <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-neutral-100 to-neutral-200">
                                        <svg class="h-12 w-12 text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3A1.5 1.5 0 001.5 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                        </svg>
                                    </div>
                                @endif
                            </a>
                            <div class="flex min-h-0 flex-1 flex-col p-5">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.12em] text-[#2563eb]">{{ $post->category }}</p>
                                <h3 class="mt-2 line-clamp-2 min-h-[2.75rem] text-[15px] font-bold leading-snug text-neutral-900 sm:text-base">
                                    <a href="{{ $post->resolveLinkUrl() }}" class="transition hover:text-[#1d4ed8]">{{ $post->headline }}</a>
                                </h3>
                                <p class="mt-auto pt-3 text-xs text-neutral-500">{{ $post->published_at->format('M j, Y') }}</p>
                            </div>
                        </article>
                    @empty
                        <p class="col-span-full text-center text-sm text-neutral-500">No stories yet — add them in the admin under Popular news.</p>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- App + Newsletter --}}
        <section class="border-t border-neutral-200 bg-neutral-50 py-10" aria-label="App and newsletter">
            <div class="store-box grid gap-4 sm:grid-cols-2">
                <div class="flex min-h-[200px] overflow-hidden rounded-2xl bg-[#ffeb3b] ring-1 ring-black/5">
                    <div class="flex flex-1 flex-col justify-center px-6 py-6">
                        <p class="text-lg font-extrabold uppercase text-neutral-900">Shop on-the-go</p>
                        <p class="mt-2 text-sm text-neutral-800">Get {{ config('app.name') }} in your pocket — browse and order anytime.</p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <span class="rounded-md bg-black px-3 py-1.5 text-[10px] font-bold uppercase tracking-wide text-white">App Store</span>
                            <span class="rounded-md bg-black px-3 py-1.5 text-[10px] font-bold uppercase tracking-wide text-white">Google Play</span>
                        </div>
                    </div>
                    <div class="relative hidden w-[42%] shrink-0 sm:block">
                        <img src="{{ asset('images/on_the_go_startframe__eq61ts4nx5g2_large.png') }}" alt="" class="absolute inset-0 h-full w-full object-cover object-center" loading="lazy">
                    </div>
                </div>
                <div class="flex min-h-[200px] overflow-hidden rounded-2xl bg-[#ffeb3b] ring-1 ring-black/5">
                    <div class="flex flex-1 flex-col justify-center px-6 py-6">
                        <p class="text-lg font-extrabold uppercase text-neutral-900">Newsletter signup</p>
                        <p class="mt-2 text-sm text-neutral-800">Deals, restocks, and Apple news — straight to your inbox.</p>
                        <form action="{{ route('products.index') }}" method="get" class="mt-4 flex flex-col gap-2 sm:flex-row">
                            <label for="newsletter-email" class="sr-only">Email</label>
                            <input id="newsletter-email" type="email" name="newsletter" placeholder="Your email" class="min-w-0 flex-1 rounded-md border border-neutral-300 px-3 py-2 text-sm text-neutral-900 placeholder:text-neutral-400">
                            <button type="button" class="rounded-md bg-[#2563eb] px-4 py-2 text-xs font-bold uppercase tracking-wide text-white transition hover:bg-[#1d4ed8]">Subscribe</button>
                        </form>
                    </div>
                    <div class="relative hidden w-[38%] shrink-0 sm:block">
                        <img src="{{ asset('images/b1_900x.webp') }}" alt="" class="absolute inset-0 h-full w-full object-cover object-center p-2" loading="lazy">
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- Product browse + filters now live on /shop --}}

    @push('scripts')
        <script>
            (function () {
                var overlay = document.querySelector('[data-shop-drawer-overlay]');
                var drawer = document.querySelector('[data-shop-drawer]');
                function openDrawer() {
                    if (!overlay || !drawer) return;
                    overlay.classList.add('store-overlay-open');
                    drawer.classList.add('store-drawer-open');
                    drawer.setAttribute('aria-hidden', 'false');
                    document.body.style.overflow = 'hidden';
                }
                function closeDrawer() {
                    if (!overlay || !drawer) return;
                    overlay.classList.remove('store-overlay-open');
                    drawer.classList.remove('store-drawer-open');
                    drawer.setAttribute('aria-hidden', 'true');
                    document.body.style.overflow = '';
                }
                document.querySelectorAll('[data-shop-drawer-open]').forEach(function (b) {
                    b.addEventListener('click', openDrawer);
                });
                document.querySelectorAll('[data-shop-drawer-close]').forEach(function (b) {
                    b.addEventListener('click', closeDrawer);
                });
                if (overlay) overlay.addEventListener('click', closeDrawer);
            })();
        </script>
    @endpush
@endsection
