@extends('layout')

@section('title', $product->name . ' — ' . config('app.name'))

@section('main_class', 'mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10')

@section('content')
    @php
        $images = $product->images;
        $primary = $images->first();
        $primaryUrl = $primary ? $primary->url() : '';
    @endphp

    <nav class="mb-8 flex flex-wrap items-center gap-x-2 gap-y-1 text-[13px] text-neutral-500" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="transition hover:text-primary-800">Home</a>
        <span class="text-neutral-300" aria-hidden="true">/</span>
        <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="transition hover:text-primary-800">{{ $product->category->name }}</a>
        <span class="text-neutral-300" aria-hidden="true">/</span>
        <span class="font-medium text-neutral-800">{{ $product->name }}</span>
    </nav>

    <div class="grid gap-10 border-b border-neutral-100 pb-12 lg:grid-cols-2 lg:gap-14 lg:items-start lg:pb-14">
        {{-- Gallery --}}
        <div class="min-w-0">
            @if ($images->isEmpty())
                <div class="flex aspect-[4/3] items-center justify-center rounded-sm border border-dashed border-neutral-300 bg-neutral-100 text-neutral-500">
                    No images
                </div>
            @else
                <div class="mx-auto w-full max-w-2xl lg:mx-0">
                <div class="relative overflow-hidden rounded-sm border border-neutral-200 bg-white">
                    <p class="sr-only" id="product-zoom-hint">Hover to magnify. Scroll while hovering to adjust zoom.</p>
                    <div
                        id="product-zoom-viewport"
                        class="relative aspect-[4/3] w-full cursor-zoom-in overflow-hidden bg-neutral-50"
                        aria-describedby="product-zoom-hint"
                        aria-label="Product image — hover to zoom"
                    >
                        <img
                            src="{{ $primaryUrl }}"
                            alt="{{ $product->name }}"
                            id="product-main-img"
                            width="1200"
                            height="900"
                            decoding="async"
                            class="pointer-events-none h-full w-full select-none object-cover will-change-transform"
                            style="transform: scale(1); transform-origin: 50% 50%; transition: transform 70ms ease-out"
                        >
                    </div>
                    <a
                        href="{{ route('products.image.open', [$product, $primary]) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        id="product-main-zoom-link"
                        class="absolute right-3 top-3 z-10 rounded-full bg-white/95 p-2 text-neutral-700 shadow-sm ring-1 ring-neutral-200/80 transition hover:bg-white hover:text-primary-800"
                        aria-label="Open image full size"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/></svg>
                    </a>
                </div>
                @if ($images->count() > 1)
                    <div class="mt-3 flex flex-wrap gap-2" role="list" aria-label="Product images">
                        @foreach ($images as $idx => $image)
                            @php
                                $variantLabel = $image->color_label ?: 'Image '.($idx + 1);
                            @endphp
                            <button
                                type="button"
                                class="product-gallery-thumb product-variant-picker relative h-16 w-16 shrink-0 overflow-hidden rounded-sm border-2 transition {{ $idx === 0 ? 'border-primary-800 ring-1 ring-primary-800/20' : 'border-neutral-200 hover:border-primary-400' }}"
                                data-full-url="{{ $image->url() }}"
                                data-open-url="{{ route('products.image.open', [$product, $image]) }}"
                                data-variant-index="{{ $idx }}"
                                aria-label="View {{ $variantLabel }}"
                                aria-current="{{ $idx === 0 ? 'true' : 'false' }}"
                            >
                                <img src="{{ $image->url() }}" alt="" class="h-full w-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
                </div>
            @endif
        </div>

        {{-- Buy box --}}
        <div>
            <div class="flex flex-wrap gap-2">
                @if ($product->discountBadgeLabel())
                    <span class="inline-flex rounded-md bg-rose-600 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-white">{{ $product->discountBadgeLabel() }}</span>
                @endif
                @if ($product->is_trending)
                    <span class="inline-flex rounded-md bg-violet-600 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-white">Trending</span>
                @endif
                @if ($product->flash_sale)
                    @if ($product->flashSaleCountdownActive())
                        <span class="inline-flex items-center gap-2 rounded-md bg-slate-900 px-2.5 py-1 text-[11px] font-semibold text-white">
                            Flash ends in
                            <span class="font-mono tabular-nums" data-flash-countdown data-end="{{ $product->sale_end_time->toIso8601String() }}">…</span>
                        </span>
                    @else
                        <span class="inline-flex rounded-md bg-orange-600 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-white">Flash sale</span>
                    @endif
                @endif
            </div>
            <h1 class="mt-3 font-serif text-3xl font-bold tracking-tight text-primary-950 sm:text-[2rem]">{{ $product->name }}</h1>

            <p class="mt-3 text-sm">
                <span class="font-medium text-neutral-600">Availability:</span>
                @if ($product->stock > 0)
                    <span class="font-semibold text-red-600">{{ $product->stock }} in stock</span>
                    @if ($product->stock <= 10)
                        <span class="ml-2 font-semibold text-amber-700">· Only {{ $product->stock }} left</span>
                    @endif
                @else
                    <span class="font-semibold text-amber-700">Out of stock</span>
                @endif
            </p>

            <div class="mt-4 flex flex-wrap items-baseline gap-3">
                @if ($product->hasActiveDiscount())
                    <span class="text-xl font-medium tabular-nums text-neutral-400 line-through sm:text-2xl">{{ format_ghs($product->listPrice()) }}</span>
                    <p class="text-3xl font-semibold text-neutral-900">{{ format_ghs($product->effectivePrice()) }}</p>
                @else
                    <p class="text-3xl font-semibold text-neutral-900">{{ format_ghs($product->price) }}</p>
                @endif
            </div>
            <p class="mt-1 text-xs text-neutral-500">Price in Ghana cedis ({{ config('store.currency_code') }}).</p>

            <div class="mt-5 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-neutral-600">
                <a href="#product-shipping" class="inline-flex items-center gap-1.5 transition hover:text-primary-800">
                    <svg class="h-4 w-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12 0v-7.635"/></svg>
                    Shipping
                </a>
                <a href="mailto:{{ config('store.email') }}?subject={{ rawurlencode('Question about: '.$product->name) }}" class="inline-flex items-center gap-1.5 transition hover:text-primary-800">
                    <svg class="h-4 w-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                    Ask about this product
                </a>
            </div>

            {{-- Size (display only) --}}
            <div class="mt-8">
                <p class="text-sm text-neutral-800">Size <span class="text-neutral-400">:</span></p>
                <div class="mt-2 flex flex-wrap gap-2">
                    <button type="button" class="storage-opt rounded-md border-2 border-primary-900 bg-white px-4 py-2 text-sm font-medium text-neutral-900" data-storage="128">128GB</button>
                    <button type="button" class="storage-opt rounded-md border-2 border-neutral-200 bg-white px-4 py-2 text-sm font-medium text-neutral-400 hover:border-neutral-300 hover:text-neutral-700" data-storage="256">256GB</button>
                </div>
            </div>

            @if ($images->count() > 1)
                <div class="mt-8">
                    <p class="text-sm text-neutral-800">Color <span class="text-neutral-400">:</span></p>
                    <div class="mt-3 flex flex-wrap gap-3">
                        @foreach ($images as $idx => $image)
                            @php
                                $swatchLabel = $image->color_label ?: 'Color '.($idx + 1);
                            @endphp
                            <div class="group relative flex flex-col items-center">
                                <button
                                    type="button"
                                    class="product-variant-swatch product-variant-picker relative h-12 w-12 shrink-0 overflow-hidden rounded-full border-2 border-neutral-200 bg-white shadow-sm ring-offset-2 transition hover:border-neutral-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-2 {{ $idx === 0 ? 'border-neutral-400 ring-2 ring-neutral-300' : '' }}"
                                    data-full-url="{{ $image->url() }}"
                                    data-open-url="{{ route('products.image.open', [$product, $image]) }}"
                                    data-variant-index="{{ $idx }}"
                                    aria-label="{{ $swatchLabel }}"
                                    aria-pressed="{{ $idx === 0 ? 'true' : 'false' }}"
                                >
                                    <img src="{{ $image->url() }}" alt="" class="h-full w-full object-cover">
                                </button>
                                <span
                                    class="pointer-events-none absolute bottom-[calc(100%+8px)] left-1/2 z-20 -translate-x-1/2 whitespace-nowrap rounded bg-neutral-900 px-2.5 py-1.5 text-xs font-medium text-white opacity-0 shadow-lg transition-opacity duration-150 group-hover:opacity-100 group-focus-within:opacity-100"
                                    role="tooltip"
                                >
                                    {{ $swatchLabel }}
                                    <span class="absolute left-1/2 top-full -translate-x-1/2 border-[6px] border-transparent border-t-neutral-900" aria-hidden="true"></span>
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($product->stock > 0)
                <div class="mt-8">
                    <p class="text-sm text-neutral-800">Quantity <span class="text-neutral-400">:</span></p>
                    <div class="mt-2 flex max-w-[11rem] items-stretch border-b border-neutral-300 bg-white">
                        <button
                            type="button"
                            id="qty-minus"
                            class="shrink-0 px-3 py-2 text-lg font-light text-neutral-600 transition hover:bg-neutral-50 hover:text-neutral-900"
                            aria-label="Decrease quantity"
                        >−</button>
                        <input
                            type="number"
                            id="qty-stepper"
                            min="1"
                            max="{{ $product->stock }}"
                            value="1"
                            class="min-w-0 flex-1 border-0 bg-transparent py-2 text-center text-sm font-medium text-neutral-900 focus:outline-none focus:ring-0"
                        >
                        <button
                            type="button"
                            id="qty-plus"
                            class="shrink-0 px-3 py-2 text-lg font-light text-neutral-600 transition hover:bg-neutral-50 hover:text-neutral-900"
                            aria-label="Increase quantity"
                        >+</button>
                    </div>
                </div>

                <form action="{{ route('cart.add', $product->id) }}" method="post" id="add-cart-form" class="mt-6">
                    @csrf
                    <input type="hidden" name="redirect" value="cart">
                    <input type="hidden" name="quantity" class="js-cart-qty" value="1">
                </form>
                <form action="{{ route('cart.add', $product->id) }}" method="post" id="buy-now-form" class="mt-3">
                    @csrf
                    <input type="hidden" name="redirect" value="checkout">
                    <input type="hidden" name="quantity" class="js-cart-qty" value="1">
                </form>

                <div class="mt-6 flex flex-wrap items-stretch gap-3">
                    <button
                        type="submit"
                        form="add-cart-form"
                        class="min-h-[48px] flex-1 bg-primary-900 px-6 py-3 text-center text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:bg-primary-950 sm:min-w-[200px]"
                    >
                        Add to cart
                    </button>
                    <span class="inline-flex min-h-[48px] min-w-[48px] items-center justify-center border border-neutral-200 bg-white text-neutral-400" title="Wishlist coming soon" aria-label="Wishlist">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                    </span>
                </div>

                <div class="mt-5">
                    <label class="flex cursor-pointer items-start gap-2 text-sm text-neutral-600">
                        <input type="checkbox" name="terms" id="terms-buy" value="1" form="buy-now-form" required class="mt-1 rounded border-neutral-300 text-primary-700 focus:ring-primary-500">
                        <span>I agree with the <a href="{{ route('home') }}#about" class="underline hover:text-primary-800">terms and conditions</a>.</span>
                    </label>
                </div>

                <button
                    type="submit"
                    form="buy-now-form"
                    class="mt-4 w-full bg-primary-100 py-3.5 text-xs font-semibold uppercase tracking-[0.18em] text-primary-950 ring-1 ring-primary-200/80 transition hover:bg-primary-200"
                >
                    Buy it now
                </button>
            @else
                <p class="mt-8 inline-flex border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold uppercase tracking-wider text-amber-900">
                    Sold out
                </p>
            @endif

            <dl class="mt-10 grid gap-3 border-t border-neutral-200 pt-8 text-sm text-neutral-600 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-neutral-400">SKU</dt>
                    <dd class="mt-0.5 font-medium text-neutral-800">DC-{{ str_pad((string) $product->id, 5, '0', STR_PAD_LEFT) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-neutral-400">Product type</dt>
                    <dd class="mt-0.5 font-medium text-neutral-800">{{ $product->category->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-neutral-400">Vendor</dt>
                    <dd class="mt-0.5 font-medium text-neutral-800">{{ config('app.name') }}</dd>
                </div>
            </dl>

            <div class="mt-6 flex items-center gap-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-neutral-400">Share</span>
                <a href="https://twitter.com/intent/tweet?text={{ rawurlencode($product->name) }}&url={{ rawurlencode(request()->url()) }}" target="_blank" rel="noopener noreferrer" class="text-neutral-400 transition hover:text-primary-700" aria-label="Share on X">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode(request()->url()) }}" target="_blank" rel="noopener noreferrer" class="text-neutral-400 transition hover:text-primary-700" aria-label="Share on Facebook">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <section class="mt-12 pt-10 lg:mt-14 lg:pt-12">
        <div class="flex flex-wrap gap-6 border-b border-neutral-200">
            <button type="button" class="product-tab border-b-2 border-primary-800 pb-3 text-sm font-semibold text-primary-950" data-tab="desc">Description</button>
            <button type="button" class="product-tab border-b-2 border-transparent pb-3 text-sm font-semibold text-neutral-500 hover:text-neutral-800" data-tab="review">Review</button>
            <button type="button" class="product-tab border-b-2 border-transparent pb-3 text-sm font-semibold text-neutral-500 hover:text-neutral-800" data-tab="custom">Custom tab</button>
        </div>

        <div id="panel-desc" class="product-panel mt-8 text-sm leading-relaxed text-neutral-700">
            @if ($product->description)
                <div class="whitespace-pre-line">{{ $product->description }}</div>
            @else
                <p class="text-neutral-500">No description has been added for this product yet.</p>
            @endif
            <div id="product-shipping" class="mt-8 rounded-sm border border-neutral-100 bg-neutral-50/80 p-5">
                <h3 class="text-sm font-bold text-primary-950">Shipping</h3>
                <p class="mt-2 text-neutral-600">We ship nationwide. Orders are processed within 1–2 business days. You will receive tracking details by email once your order ships.</p>
            </div>
        </div>

        <div id="panel-review" class="product-panel mt-8 hidden text-sm text-neutral-600">
            <p>No reviews yet. Be the first to share your experience with this product.</p>
        </div>

        <div id="panel-custom" class="product-panel mt-8 hidden text-sm leading-relaxed text-neutral-700">
            <p class="font-medium text-primary-950">Warranty & support</p>
            <p class="mt-2 text-neutral-600">Genuine products are backed by our store support. Contact us for warranty questions or returns within the policy window.</p>
        </div>
    </section>

    @if ($relatedProducts->isNotEmpty())
        <section class="mt-14 border-t border-neutral-100 pt-12 lg:mt-16">
            <h2 class="text-lg font-bold uppercase tracking-[0.12em] text-primary-950">Related products</h2>
            <p class="mt-1 text-sm text-neutral-500">More from {{ $product->category->name }}</p>
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($relatedProducts as $p)
                    @include('products.partials.card', ['product' => $p])
                @endforeach
            </div>
        </section>
    @endif
@endsection

@push('scripts')
    <script>
        (function () {
            var qty = document.getElementById('qty-stepper');
            var syncQty = function () {
                if (!qty) return;
                var v = Math.max(1, Math.min(parseInt(qty.value, 10) || 1, parseInt(qty.getAttribute('max'), 10) || 999));
                qty.value = v;
                document.querySelectorAll('.js-cart-qty').forEach(function (el) { el.value = v; });
            };
            if (qty) {
                qty.addEventListener('change', syncQty);
                qty.addEventListener('input', syncQty);
                syncQty();
                var minus = document.getElementById('qty-minus');
                var plus = document.getElementById('qty-plus');
                var maxQ = parseInt(qty.getAttribute('max'), 10) || 999;
                if (minus) {
                    minus.addEventListener('click', function () {
                        var v = parseInt(qty.value, 10) || 1;
                        qty.value = Math.max(1, v - 1);
                        syncQty();
                    });
                }
                if (plus) {
                    plus.addEventListener('click', function () {
                        var v = parseInt(qty.value, 10) || 1;
                        qty.value = Math.min(maxQ, v + 1);
                        syncQty();
                    });
                }
            }

            var productZoom = (function () {
                var viewport = document.getElementById('product-zoom-viewport');
                var img = document.getElementById('product-main-img');
                if (!viewport || !img) {
                    return { reset: function () {} };
                }
                if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    return { reset: function () {} };
                }
                var zoom = 2.2;
                var minZ = 1.5;
                var maxZ = 3.5;
                var hovering = false;

                function clamp(n, a, b) {
                    return Math.max(a, Math.min(b, n));
                }

                function setOriginFromEvent(e) {
                    var rect = viewport.getBoundingClientRect();
                    var x = ((e.clientX - rect.left) / rect.width) * 100;
                    var y = ((e.clientY - rect.top) / rect.height) * 100;
                    img.style.transformOrigin = clamp(x, 0, 100) + '% ' + clamp(y, 0, 100) + '%';
                }

                function applyScale() {
                    img.style.transform = 'scale(' + (hovering ? zoom : 1) + ')';
                }

                viewport.addEventListener('mouseenter', function () {
                    hovering = true;
                    applyScale();
                });
                viewport.addEventListener('mouseleave', function () {
                    hovering = false;
                    zoom = 2.2;
                    img.style.transform = 'scale(1)';
                    img.style.transformOrigin = '50% 50%';
                });
                viewport.addEventListener('mousemove', function (e) {
                    if (!hovering) return;
                    setOriginFromEvent(e);
                });
                viewport.addEventListener('wheel', function (e) {
                    if (!hovering) return;
                    e.preventDefault();
                    zoom = clamp(zoom + (e.deltaY > 0 ? -0.12 : 0.12), minZ, maxZ);
                    setOriginFromEvent(e);
                    applyScale();
                }, { passive: false });

                return {
                    reset: function () {
                        zoom = 2.2;
                        hovering = false;
                        img.style.transform = 'scale(1)';
                        img.style.transformOrigin = '50% 50%';
                    },
                };
            })();

            function applyVariantUi(activeIndex) {
                document.querySelectorAll('.product-variant-picker').forEach(function (el) {
                    var i = el.getAttribute('data-variant-index');
                    var active = String(i) === String(activeIndex);
                    if (el.classList.contains('product-variant-swatch')) {
                        if (active) {
                            el.classList.remove('border-neutral-200');
                            el.classList.add('border-neutral-400', 'ring-2', 'ring-neutral-300');
                            el.setAttribute('aria-pressed', 'true');
                        } else {
                            el.classList.remove('border-neutral-400', 'ring-2', 'ring-neutral-300');
                            el.classList.add('border-neutral-200');
                            el.setAttribute('aria-pressed', 'false');
                        }
                    }
                    if (el.classList.contains('product-gallery-thumb')) {
                        if (active) {
                            el.classList.remove('border-neutral-200', 'hover:border-primary-400');
                            el.classList.add('border-primary-800', 'ring-1', 'ring-primary-800/20');
                            el.setAttribute('aria-current', 'true');
                        } else {
                            el.classList.remove('border-primary-800', 'ring-1', 'ring-primary-800/20');
                            el.classList.add('border-neutral-200', 'hover:border-primary-400');
                            el.setAttribute('aria-current', 'false');
                        }
                    }
                });
            }

            document.querySelectorAll('.product-variant-picker').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var url = btn.getAttribute('data-full-url');
                    var openUrl = btn.getAttribute('data-open-url');
                    var idx = btn.getAttribute('data-variant-index');
                    var main = document.getElementById('product-main-img');
                    var link = document.getElementById('product-main-zoom-link');
                    productZoom.reset();
                    if (main && url) main.src = url;
                    if (link && openUrl) link.href = openUrl;
                    applyVariantUi(idx);
                });
            });

            document.querySelectorAll('.storage-opt').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.storage-opt').forEach(function (b) {
                        b.classList.remove('border-primary-900', 'text-neutral-900');
                        b.classList.add('border-neutral-200', 'text-neutral-400');
                    });
                    btn.classList.remove('border-neutral-200', 'text-neutral-400', 'text-neutral-700');
                    btn.classList.add('border-primary-900', 'text-neutral-900');
                });
            });

            var tabs = document.querySelectorAll('.product-tab');
            var panels = { desc: document.getElementById('panel-desc'), review: document.getElementById('panel-review'), custom: document.getElementById('panel-custom') };
            tabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    var name = tab.getAttribute('data-tab');
                    tabs.forEach(function (t) {
                        var active = t.getAttribute('data-tab') === name;
                        t.classList.toggle('border-primary-800', active);
                        t.classList.toggle('text-primary-950', active);
                        t.classList.toggle('border-transparent', !active);
                        t.classList.toggle('text-neutral-500', !active);
                    });
                    Object.keys(panels).forEach(function (key) {
                        if (panels[key]) panels[key].classList.toggle('hidden', key !== name);
                    });
                });
            });
        })();
    </script>
@endpush
