@php
    $tel = trim((string) config('store.phone_tel'));
    if ($tel === '') {
        $tel = preg_replace('/\D+/', '', (string) config('store.phone'));
    }
    $storeName = config('app.name');
    $address = trim((string) config('store.address', ''));
    $social = config('store.social', []);
    $wishlistCount = \App\Support\WishlistSession::count();
    $currencyLabel = 'Ghana ('.config('store.currency_symbol').' '.config('store.currency_code').')';
    $categoriesCollection = ($categories ?? collect());
    $categoryCount = $categoriesCollection->count();
    $catMegaCols = $categoryCount > 0
        ? $categoriesCollection->chunk(max(1, (int) ceil($categoryCount / 4)))
        : collect();
@endphp

{{-- Tier 1: top bar (hidden on small screens — welcome, currency, language) --}}
<div class="hidden border-b border-white/10 bg-[#00479a] text-[12px] text-white sm:text-[13px] md:block">
    <div class="store-box flex flex-col gap-2 py-2 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between sm:gap-y-2">
        <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
            <p class="text-white/95">
                <span class="font-medium text-[#ffd700]">{{ config('store.welcome_prefix') }}</span>
                <span class="font-semibold">{{ $storeName }}</span>
                <span class="text-white/50"> · </span>
                <span class="text-white/75">Mobile phones &amp; tech</span>
            </p>
            <div class="flex items-center gap-2 border-l border-white/20 pl-3">
                @foreach (['facebook' => 'fa-brands fa-facebook-f', 'instagram' => 'fa-brands fa-instagram', 'tiktok' => 'fa-brands fa-tiktok', 'youtube' => 'fa-brands fa-youtube', 'pinterest' => 'fa-brands fa-pinterest-p'] as $key => $icon)
                    @php $href = $social[$key] ?? '#'; @endphp
                    <a href="{{ $href }}" class="text-white/80 transition-colors hover:text-[#ffd700]" aria-label="{{ ucfirst($key) }}" @if ($href !== '#') target="_blank" rel="noopener noreferrer" @endif>
                        <i class="{{ $icon }} text-[14px]" aria-hidden="true"></i>
                    </a>
                @endforeach
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-white/90">
            @if ($address !== '')
                <span class="inline-flex max-w-[min(100%,280px)] items-start gap-1.5 text-white/85">
                    <i class="fa-solid fa-location-dot mt-0.5 shrink-0 text-[#ffd700]" aria-hidden="true"></i>
                    <span>{{ $address }}</span>
                </span>
            @endif
            <span class="hidden h-4 w-px bg-white/25 sm:block" aria-hidden="true"></span>
            <span class="inline-flex items-center gap-1.5 font-medium">
                <i class="fa-solid fa-coins text-[#ffd700]" aria-hidden="true"></i>
                {{ $currencyLabel }}
            </span>
            <span class="hidden h-4 w-px bg-white/25 sm:block" aria-hidden="true"></span>
            <span class="inline-flex items-center gap-1">
                <i class="fa-solid fa-globe text-white/70" aria-hidden="true"></i>
                English
                <i class="fa-solid fa-chevron-down text-[10px] text-white/50" aria-hidden="true"></i>
            </span>
        </div>
    </div>
</div>

<header class="sticky top-0 z-50 shadow-sm shadow-slate-900/10">
    {{-- Tier 2: main bar — brand blue; interactive items use hover:text-[#ffd700] (store yellow) --}}
    <div class="border-b border-white/10 bg-[#0057b8]">
        <div class="store-box flex flex-col gap-3 py-3 sm:gap-4 lg:flex-row lg:items-center lg:gap-4">
            <div class="flex min-w-0 items-center gap-2 sm:gap-3 lg:shrink-0">
                <button
                    type="button"
                    class="inline-flex h-11 w-11 shrink-0 items-center justify-center text-white transition-colors hover:text-[#ffd700] lg:hidden"
                    aria-expanded="false"
                    aria-controls="store-mobile-nav"
                    data-store-nav-toggle
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                </button>
                <a href="{{ route('home') }}" class="group flex min-w-0 items-center gap-2 text-white transition-colors hover:text-[#ffd700]">
                    <span class="truncate text-2xl font-extrabold tracking-tight lowercase sm:text-[1.65rem]">{{ $storeName }}</span>
                </a>
            </div>

            <form action="{{ route('products.index') }}" method="get" class="order-last flex w-full min-w-0 lg:order-none lg:mx-4 lg:max-w-xl lg:flex-1" role="search">
                <label for="store-header-search" class="sr-only">Search products</label>
                <div class="flex w-full items-center rounded-full border border-white/20 bg-white pl-3 shadow-inner shadow-slate-900/5 sm:pl-4">
                    <i class="fa-solid fa-magnifying-glass text-slate-400" aria-hidden="true"></i>
                    <input
                        type="search"
                        name="search"
                        id="store-header-search"
                        value="{{ trim((string) request('search', '')) }}"
                        placeholder="I'm looking for…"
                        class="store-input-focus min-w-0 flex-1 border-0 bg-transparent py-2.5 pl-2 pr-3 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-0 sm:py-3 sm:pl-3 sm:pr-4"
                    >
                    <button type="submit" class="mr-1 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#0057b8] text-white transition-colors hover:bg-[#ffd700] hover:text-[#0057b8] sm:mr-1.5 sm:h-10 sm:w-10" aria-label="Search">
                        <i class="fa-solid fa-arrow-right text-sm" aria-hidden="true"></i>
                    </button>
                </div>
            </form>

            <div class="flex flex-wrap items-center justify-between gap-y-3 sm:justify-end lg:ml-auto lg:shrink-0 lg:gap-x-6 xl:gap-x-10">
                @if ($tel !== '')
                    <a href="tel:{{ $tel }}" class="hidden items-center gap-3 text-white transition-colors hover:text-[#ffd700] md:inline-flex" aria-label="Call {{ config('store.phone') }}">
                        {{-- FA free: headset is solid; regular/light are Pro — outline style matches reference --}}
                        <i class="fa-solid fa-headset shrink-0 text-[1.4rem] leading-none text-inherit opacity-95" aria-hidden="true"></i>
                        <span class="text-left leading-tight text-inherit">
                            <span class="block text-sm font-normal">Need Any Help?</span>
                            <span class="block text-sm font-bold">{{ config('store.phone') }}</span>
                        </span>
                    </a>
                @endif

                <div class="flex flex-wrap items-center gap-x-5 gap-y-2 sm:gap-x-6 md:gap-x-8">
                    @guest
                        <a href="{{ route('login') }}" class="flex items-center gap-3 text-white transition-colors hover:text-[#ffd700]" aria-label="Log in to your account">
                            <i class="fa-regular fa-user shrink-0 text-[1.5rem] leading-none text-inherit" aria-hidden="true"></i>
                            <span class="text-left leading-tight text-inherit">
                                <span class="block text-sm font-normal">Log In</span>
                                <span class="block text-sm font-bold">Account</span>
                            </span>
                        </a>
                    @else
                        @include('partials.user-profile-menu', ['context' => 'store', 'store_header_dark' => true, 'store_header_icon_rows' => true])
                    @endguest

                    <a href="{{ route('home') }}#wishlist" class="relative inline-flex h-11 w-11 shrink-0 items-center justify-center text-white transition-colors hover:text-[#ffd700]" aria-label="Wishlist">
                        <i class="fa-regular fa-heart text-[1.5rem] leading-none text-inherit" aria-hidden="true"></i>
                        <span class="absolute -right-0.5 -top-0.5 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-[#C44D3D] px-0.5 text-[10px] font-bold leading-none text-white">{{ $wishlistCount > 99 ? '99+' : $wishlistCount }}</span>
                    </a>

                    <a
                        href="{{ route('cart.index') }}"
                        data-store-cart-link
                        data-cart-drawer-open
                        class="{{ ($cartCount ?? 0) > 0 ? 'store-cart-pulse' : '' }} flex items-center gap-3 text-white transition-colors hover:text-[#ffd700]"
                        aria-label="Shopping cart"
                    >
                        <span class="relative inline-flex h-11 w-11 shrink-0 items-center justify-center text-inherit">
                            <i class="fa-solid fa-cart-shopping text-[1.5rem] leading-none text-inherit" aria-hidden="true"></i>
                            <span class="absolute -right-0.5 -top-0.5 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-[#C44D3D] px-0.5 text-[10px] font-bold leading-none text-white" data-store-cart-count>{{ ($cartCount ?? 0) > 99 ? '99+' : ($cartCount ?? 0) }}</span>
                        </span>
                        <span class="hidden flex-col text-left leading-tight text-inherit sm:flex sm:min-w-0">
                            <span class="block text-sm font-normal">Cart</span>
                            <span class="block text-sm font-bold tabular-nums" data-store-cart-total>{{ format_ghs($cartTotal ?? 0) }}</span>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Tier 3: yellow navigation bar + full-width mega menus (click to toggle, lg+) --}}
    <div class="relative hidden border-b border-slate-900/10 bg-[#ffd700] lg:block" data-store-mega-root>
        <div class="store-box flex flex-wrap items-center justify-between gap-x-4 gap-y-2 py-0">
            <nav class="flex flex-wrap items-center gap-x-1" aria-label="Primary">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-1 px-3 py-3.5 text-sm font-semibold text-slate-900 transition-colors hover:text-[#0057b8]">Home</a>

                <button
                    type="button"
                    id="store-mega-trigger-shop"
                    class="group store-mega-trigger inline-flex items-center gap-1 border-b-2 border-transparent px-3 py-3.5 text-sm font-bold text-slate-900 transition-colors hover:text-[#0057b8] aria-expanded:border-[#0057b8] aria-expanded:text-[#0057b8]"
                    aria-expanded="false"
                    aria-haspopup="true"
                    aria-controls="store-mega-shop"
                    data-store-mega-target="shop"
                >
                    Shop
                    <i class="store-mega-chevron fa-solid fa-chevron-down text-[10px] text-slate-800 transition-transform duration-200" aria-hidden="true"></i>
                </button>

                <button
                    type="button"
                    id="store-mega-trigger-products"
                    class="group store-mega-trigger inline-flex items-center gap-1 border-b-2 border-transparent px-3 py-3.5 text-sm font-bold text-slate-900 transition-colors hover:text-[#0057b8] aria-expanded:border-[#0057b8] aria-expanded:text-[#0057b8]"
                    aria-expanded="false"
                    aria-haspopup="true"
                    aria-controls="store-mega-products"
                    data-store-mega-target="products"
                >
                    Products
                    <i class="store-mega-chevron fa-solid fa-chevron-down text-[10px] text-slate-800 transition-transform duration-200" aria-hidden="true"></i>
                </button>

                <button
                    type="button"
                    id="store-mega-trigger-pages"
                    class="group store-mega-trigger inline-flex items-center gap-1 border-b-2 border-transparent px-3 py-3.5 text-sm font-bold text-slate-900 transition-colors hover:text-[#0057b8] aria-expanded:border-[#0057b8] aria-expanded:text-[#0057b8]"
                    aria-expanded="false"
                    aria-haspopup="true"
                    aria-controls="store-mega-pages"
                    data-store-mega-target="pages"
                >
                    Pages
                    <i class="store-mega-chevron fa-solid fa-chevron-down text-[10px] text-slate-800 transition-transform duration-200" aria-hidden="true"></i>
                </button>

                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1 px-3 py-3.5 text-sm font-semibold text-slate-900 transition-colors hover:text-[#0057b8]">New arrivals</a>

                <button
                    type="button"
                    id="store-mega-trigger-categories"
                    class="group store-mega-trigger inline-flex items-center gap-1 border-b-2 border-transparent px-3 py-3.5 text-sm font-bold text-slate-900 transition-colors hover:text-[#0057b8] aria-expanded:border-[#0057b8] aria-expanded:text-[#0057b8]"
                    aria-expanded="false"
                    aria-haspopup="true"
                    aria-controls="store-mega-categories"
                    data-store-mega-target="categories"
                >
                    Categories
                    <i class="store-mega-chevron fa-solid fa-chevron-down text-[10px] text-slate-800 transition-transform duration-200" aria-hidden="true"></i>
                </button>
            </nav>

            <nav class="flex flex-wrap items-center gap-x-1 text-sm font-medium text-slate-800" aria-label="Secondary">
                <a href="mailto:{{ config('store.email') }}" class="px-2 py-3.5 transition-colors hover:text-[#0057b8]">Contact us</a>
                <span class="h-3 w-px bg-slate-900/20" aria-hidden="true"></span>
                <a href="{{ route('home') }}#features" class="px-2 py-3.5 transition-colors hover:text-[#0057b8]">About us</a>
                <span class="h-3 w-px bg-slate-900/20" aria-hidden="true"></span>
                <a href="{{ route('tracking.index') }}" class="px-2 py-3.5 transition-colors hover:text-[#0057b8]">Help center</a>
                <span class="h-3 w-px bg-slate-900/20" aria-hidden="true"></span>
                <a href="{{ route('home') }}" class="px-2 py-3.5 transition-colors hover:text-[#0057b8]">Our store</a>
            </nav>
        </div>

        <div id="store-mega-shop" class="store-mega-panel hidden absolute left-0 right-0 top-full z-[60] rounded-b-xl border-t border-slate-200/90 bg-white shadow-lg shadow-slate-300/40" data-store-mega-panel role="region" aria-labelledby="store-mega-trigger-shop">
            <div class="store-box py-8">
                <div class="flex flex-col gap-8 lg:flex-row lg:items-stretch lg:gap-10">
                    <div class="lg:w-[min(100%,380px)] lg:shrink-0">
                        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-slate-50">
                            <img src="{{ mega_nav_decor_url('a') }}" alt="" class="aspect-[4/3] w-full object-cover sm:aspect-[5/3]" loading="lazy" decoding="async">
                        </div>
                    </div>
                    <div class="flex min-w-0 flex-1 flex-col justify-between gap-8 lg:flex-row lg:gap-12">
                        <div class="min-w-0 flex-1">
                            <p class="text-[13px] font-bold text-slate-900">Quick links</p>
                            <ul class="mt-3 space-y-2 text-[13px] text-slate-600">
                                <li><a href="{{ route('products.index') }}" class="transition-colors hover:text-[#0057b8]">Browse &amp; search</a></li>
                                <li><a href="{{ route('products.index') }}#store-search" class="transition-colors hover:text-[#0057b8]">Jump to filters</a></li>
                                <li><a href="{{ route('cart.index') }}" class="transition-colors hover:text-[#0057b8]">Shopping cart</a></li>
                            </ul>
                            <p class="mt-6 text-[13px] font-bold text-slate-900">Discover</p>
                            <ul class="mt-3 space-y-2 text-[13px] text-slate-600">
                                <li><a href="{{ route('home') }}#features" class="transition-colors hover:text-[#0057b8]">Why shop with us</a></li>
                                <li><a href="{{ route('tracking.index') }}" class="transition-colors hover:text-[#0057b8]">Track order</a></li>
                            </ul>
                        </div>
                        <div class="flex w-full flex-col justify-between rounded-2xl border border-slate-100 bg-slate-50/90 p-6 lg:max-w-[280px]">
                            <div>
                                <p class="text-sm font-bold text-slate-900">{{ $storeName }}</p>
                                <p class="mt-2 text-[13px] leading-relaxed text-slate-600">Genuine Apple &amp; accessories — search and filter on the store page.</p>
                            </div>
                            <a href="{{ route('products.index') }}" class="mt-6 inline-flex w-full items-center justify-center rounded-xl bg-[#0057b8] px-4 py-3 text-sm font-semibold text-white transition-colors hover:bg-[#00479a]">Open store</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="store-mega-products" class="store-mega-panel hidden absolute left-0 right-0 top-full z-[60] rounded-b-xl border-t border-slate-200/90 bg-white shadow-lg shadow-slate-300/40" data-store-mega-panel role="region" aria-labelledby="store-mega-trigger-products">
            <div class="store-box py-8">
                @if ($categoriesCollection->isNotEmpty())
                    <div class="flex flex-col gap-8 lg:flex-row lg:items-start lg:gap-12">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-slate-900">Shop by category</p>
                            <p class="mt-1 text-[13px] text-slate-500">Pick a category — filters and price range are on the store page.</p>
                            <div class="mt-6 grid gap-x-8 gap-y-2 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach ($categoriesCollection as $cat)
                                    <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="flex items-center gap-2 rounded-lg py-1.5 text-[13px] font-medium text-slate-700 transition-colors hover:text-[#0057b8]">
                                        <i class="{{ category_fa_classes($cat->slug, $cat->name) }} w-5 shrink-0 text-center text-[14px] text-slate-400" aria-hidden="true"></i>
                                        <span>{{ $cat->name }}</span>
                                    </a>
                                @endforeach
                            </div>
                            <p class="mt-8">
                                <a href="{{ route('products.index') }}" class="text-sm font-semibold text-[#0057b8] transition-colors hover:text-[#00479a]">Browse full store →</a>
                            </p>
                        </div>
                        <div class="mx-auto w-full max-w-[320px] shrink-0 overflow-hidden rounded-2xl border border-slate-100 bg-slate-50 lg:mx-0">
                            <img src="{{ category_mega_image_url($categoriesCollection->first()->slug, $categoriesCollection->first()->name) }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                        </div>
                    </div>
                @else
                    <p class="text-center text-sm text-slate-600">Categories will appear here when available.</p>
                @endif
            </div>
        </div>

        <div id="store-mega-pages" class="store-mega-panel hidden absolute left-0 right-0 top-full z-[60] rounded-b-xl border-t border-slate-200/90 bg-white shadow-lg shadow-slate-300/40" data-store-mega-panel role="region" aria-labelledby="store-mega-trigger-pages">
            <div class="store-box py-8">
                <div class="flex flex-col gap-8 md:flex-row md:items-start md:gap-10">
                    <div class="mx-auto w-full max-w-[340px] shrink-0 overflow-hidden rounded-2xl border border-slate-100 bg-slate-50 md:mx-0">
                        <img src="{{ mega_nav_decor_url('b') }}" alt="" class="aspect-[16/11] w-full object-cover" loading="lazy" decoding="async">
                    </div>
                    <div class="grid min-w-0 flex-1 gap-8 sm:grid-cols-2">
                        <div>
                            <p class="text-sm font-bold text-slate-900">Orders &amp; service</p>
                            <ul class="mt-3 space-y-2 text-[13px] text-slate-600">
                                <li><a href="{{ route('tracking.index') }}" class="transition-colors hover:text-[#0057b8]">Track order</a></li>
                                <li><a href="{{ route('cart.index') }}" class="transition-colors hover:text-[#0057b8]">Shopping cart</a></li>
                                <li><a href="{{ route('home') }}#features" class="transition-colors hover:text-[#0057b8]">Why shop with us</a></li>
                            </ul>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-900">Company</p>
                            <ul class="mt-3 space-y-2 text-[13px] text-slate-600">
                                <li><a href="mailto:{{ config('store.email') }}" class="transition-colors hover:text-[#0057b8]">Contact us</a></li>
                                <li><a href="{{ route('home') }}#features" class="transition-colors hover:text-[#0057b8]">About us</a></li>
                                <li><a href="{{ route('home') }}" class="transition-colors hover:text-[#0057b8]">Our store</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="store-mega-categories" class="store-mega-panel hidden absolute left-0 right-0 top-full z-[60] rounded-b-xl border-t border-slate-200/90 bg-white shadow-lg shadow-slate-300/40" data-store-mega-panel role="region" aria-labelledby="store-mega-trigger-categories">
            <div class="store-box py-8">
                @if ($catMegaCols->isNotEmpty())
                    @php $bannerCat = $categoriesCollection->first(); @endphp
                    <div class="mb-8 overflow-hidden rounded-2xl border border-slate-100 bg-slate-50">
                        <div class="flex flex-col md:flex-row md:items-stretch">
                            <div class="md:w-2/5 md:max-w-md md:shrink-0">
                                <img src="{{ category_mega_image_url($bannerCat->slug, $bannerCat->name) }}" alt="" class="h-44 w-full object-cover md:h-full md:min-h-[200px]" loading="lazy" decoding="async">
                            </div>
                            <div class="flex flex-1 flex-col justify-center px-6 py-5 md:py-6">
                                <p class="text-sm font-bold text-slate-900">Find what you need</p>
                                <p class="mt-1 text-[13px] text-slate-600">Choose a category below — the store page has a full sidebar for categories, price, color, and sorting.</p>
                            </div>
                        </div>
                    </div>
                    <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4 lg:gap-10">
                        @foreach ($catMegaCols as $colCats)
                            <div>
                                <p class="text-[13px] font-bold text-slate-900">Browse</p>
                                <ul class="mt-3 space-y-2 text-[13px] text-slate-600">
                                    @foreach ($colCats as $cat)
                                        <li>
                                            <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="transition-colors hover:text-[#0057b8]">{{ $cat->name }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                    <p class="mt-8 text-center">
                        <a href="{{ route('products.index') }}" class="text-sm font-semibold text-[#0057b8] transition-colors hover:text-[#00479a]">Open store →</a>
                    </p>
                @else
                    <p class="text-center text-sm text-slate-600">No categories yet.</p>
                @endif
            </div>
        </div>
    </div>

    <div id="store-mobile-nav" class="hidden border-b border-slate-200 bg-white lg:hidden" data-store-mobile-panel>
        <div class="store-box max-h-[min(70vh,520px)] overflow-y-auto py-4">
            <nav class="flex flex-col gap-1" aria-label="Mobile">
                <a href="{{ route('home') }}" class="rounded-xl px-3 py-3 text-sm font-semibold text-slate-800 hover:bg-slate-50">Home</a>
                <a href="{{ route('products.index') }}" class="rounded-xl px-3 py-3 text-sm font-semibold text-slate-800 hover:bg-slate-50">Shop — all products</a>
                <a href="{{ route('tracking.index') }}" class="rounded-xl px-3 py-3 text-sm font-semibold text-slate-800 hover:bg-slate-50">Track order</a>
                <a href="{{ route('home') }}#features" class="rounded-xl px-3 py-3 text-sm font-semibold text-slate-800 hover:bg-slate-50">Why shop with us</a>
                <p class="px-3 pt-2 text-[11px] font-bold uppercase tracking-wide text-slate-400">Categories</p>
                @foreach (($categories ?? collect())->take(12) as $cat)
                    <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="rounded-xl px-3 py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-[#ffd700]">{{ $cat->name }}</a>
                @endforeach
                <p class="mt-2 border-t border-slate-100 px-3 pt-3 text-[11px] font-bold uppercase tracking-wide text-slate-400">More</p>
                <a href="mailto:{{ config('store.email') }}" class="rounded-xl px-3 py-2.5 text-sm text-slate-600 hover:bg-slate-50">Contact us</a>
                <a href="{{ route('home') }}#wishlist" class="rounded-xl px-3 py-2.5 text-sm text-slate-600 hover:bg-slate-50">Wishlist</a>
                @guest
                    <a href="{{ route('login') }}" class="rounded-xl px-3 py-3 text-sm font-semibold text-[#0057b8] transition-colors hover:bg-slate-50 hover:text-[#ffd700]">Log in</a>
                @endguest
            </nav>
        </div>
    </div>
</header>

@push('scripts')
<script>
(function () {
    var root = document.querySelector('[data-store-mega-root]');
    if (!root) return;
    var triggers = root.querySelectorAll('[data-store-mega-target]');
    var panels = root.querySelectorAll('[data-store-mega-panel]');

    function closeAll() {
        triggers.forEach(function (btn) {
            btn.setAttribute('aria-expanded', 'false');
        });
        panels.forEach(function (panel) {
            panel.classList.add('hidden');
        });
    }

    function openPanel(id) {
        closeAll();
        var panel = document.getElementById('store-mega-' + id);
        var btn = root.querySelector('[data-store-mega-target="' + id + '"]');
        if (!panel || !btn) return;
        panel.classList.remove('hidden');
        btn.setAttribute('aria-expanded', 'true');
    }

    function toggle(id) {
        var btn = root.querySelector('[data-store-mega-target="' + id + '"]');
        var panel = document.getElementById('store-mega-' + id);
        if (!btn || !panel) return;
        var isOpen = btn.getAttribute('aria-expanded') === 'true';
        if (isOpen) {
            closeAll();
        } else {
            openPanel(id);
        }
    }

    triggers.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var id = btn.getAttribute('data-store-mega-target');
            if (id) toggle(id);
        });
    });

    document.addEventListener('click', function (e) {
        if (!root.contains(e.target)) closeAll();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeAll();
    });

    root.addEventListener('click', function (e) {
        var link = e.target.closest('a');
        if (link && !e.target.closest('[data-store-mega-target]')) {
            closeAll();
        }
    });
})();
</script>
@endpush
