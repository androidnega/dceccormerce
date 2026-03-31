@php
    $img = $product->images->first();
    $variant = $variant ?? 'tab';
    $stars = 4 + ($product->id % 2);
    $compareAt = $product->hasActiveDiscount()
        ? $product->listPrice()
        : (in_array($variant, ['sale', 'sale-strip'], true) ? round((float) $product->price * 1.12, 2) : null);
    $compareAtCompact = $variant === 'sale-compact'
        ? ($product->hasActiveDiscount()
            ? $product->listPrice()
            : null)
        : $compareAt;
    $displayPrice = $product->effectivePrice();
    $saleStripImageUrl = $saleStripImageUrl ?? null;
    $saleStripDisplaySrc = (($variant === 'sale-strip' || $variant === 'sale-compact') && $saleStripImageUrl) ? $saleStripImageUrl : ($img ? $img->url() : null);
    $inWishlist = ($variant === 'tab') ? \App\Support\WishlistSession::has($product->id) : false;
@endphp
@if ($variant === 'sale-strip')
    <article class="group relative overflow-hidden rounded-2xl border border-zinc-200/70 bg-white shadow-[0_1px_3px_rgba(0,0,0,0.04)] ring-1 ring-zinc-950/[0.02] transition duration-300 ease-out hover:border-zinc-300/80 hover:shadow-[0_8px_30px_-8px_rgba(0,0,0,0.1)]">
        <div class="flex min-h-[124px] sm:min-h-[140px] md:min-h-[152px]">
            <div class="relative w-[44%] min-w-[120px] max-w-[240px] shrink-0 overflow-hidden bg-zinc-100">
                <span class="absolute left-2.5 top-2.5 z-10 rounded-full bg-zinc-900 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-white shadow-sm">Sale</span>
                <a href="{{ route('products.show', $product) }}" class="relative flex h-full min-h-[124px] w-full items-stretch sm:min-h-[140px] md:min-h-[152px]">
                    @if ($saleStripDisplaySrc)
                        <img src="{{ $saleStripDisplaySrc }}" alt="{{ $product->name }}" class="h-full w-full object-cover object-center transition duration-500 ease-out group-hover:scale-[1.03]">
                    @else
                        <div class="flex min-h-full w-full items-center justify-center text-xs text-zinc-400">No image</div>
                    @endif
                </a>
            </div>
            <div class="flex min-w-0 flex-1 flex-col justify-center gap-1 px-4 py-3.5 sm:gap-1.5 sm:px-5 sm:py-4">
                <div class="flex gap-0.5 text-amber-400" aria-hidden="true">
                    @for ($i = 0; $i < 5; $i++)
                        <svg class="h-3.5 w-3.5 {{ $i < $stars ? 'text-amber-400' : 'text-zinc-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                <h2 class="line-clamp-2 text-sm font-semibold leading-snug tracking-tight text-zinc-900 sm:text-[15px]">
                    <a href="{{ route('products.show', $product) }}" class="transition hover:text-indigo-600">{{ $product->name }}</a>
                </h2>
                <div class="mt-0.5 flex flex-wrap items-baseline gap-x-2 gap-y-0.5">
                    @if ($compareAt !== null)
                        <span class="text-xs tabular-nums text-zinc-400 line-through sm:text-sm">{{ format_ghs($compareAt) }}</span>
                    @endif
                    <span class="text-base font-bold tabular-nums tracking-tight text-zinc-900 sm:text-lg">{{ format_ghs($displayPrice) }}</span>
                </div>
            </div>
        </div>
    </article>
@elseif ($variant === 'sale-compact')
    <article
        data-sale-spotlight-card
        data-product-url="{{ route('products.show', $product) }}"
        class="group relative flex flex-col overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm transition duration-200 hover:shadow-md"
    >
        <span class="absolute left-2.5 top-2.5 z-10 rounded-full bg-[#0057b8] px-3 py-1.5 text-[11px] font-extrabold uppercase leading-none tracking-wide text-white shadow-md sm:left-3 sm:top-3 sm:px-3.5 sm:py-2 sm:text-xs">Sale!</span>
        <a href="{{ route('products.show', $product) }}" class="relative flex h-[7.5rem] w-full items-center justify-center bg-zinc-50 px-3 pb-2 pt-11 sm:h-36 sm:pt-12">
            @if ($saleStripDisplaySrc)
                <img src="{{ $saleStripDisplaySrc }}" alt="{{ $product->name }}" class="max-h-full max-w-full object-contain object-center transition duration-300 group-hover:scale-[1.03]" loading="lazy">
            @else
                <span class="text-xs text-zinc-400">No image</span>
            @endif
        </a>
        <div class="flex flex-col items-center px-3 pb-3 pt-1.5 text-center">
            <div class="flex justify-center gap-0.5 text-amber-400" aria-hidden="true">
                @for ($i = 0; $i < 5; $i++)
                    <svg class="h-3 w-3 {{ $i < $stars ? 'text-amber-400' : 'text-zinc-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                @endfor
            </div>
            <h2 class="mt-1.5 line-clamp-2 min-h-[2.5rem] text-sm font-semibold leading-snug text-zinc-900">
                <a href="{{ route('products.show', $product) }}" class="transition hover:text-[#0057b8]">{{ $product->name }}</a>
            </h2>
            <div class="mt-2 flex flex-wrap items-baseline justify-center gap-x-2.5 gap-y-1">
                @if ($compareAtCompact !== null && (float) $compareAtCompact > (float) $displayPrice)
                    <span class="text-sm tabular-nums text-zinc-400 line-through sm:text-base">{{ format_ghs($compareAtCompact) }}</span>
                @endif
                <span class="text-lg font-bold tabular-nums text-rose-600 sm:text-xl">{{ format_ghs($displayPrice) }}</span>
            </div>
        </div>
    </article>
@else
<article data-product-card @class([
    'store-card-shine group relative flex h-full w-full flex-col overflow-hidden rounded-2xl border border-zinc-200/70 bg-white shadow-[0_1px_3px_rgba(0,0,0,0.04)] ring-1 ring-zinc-950/[0.02] transition duration-300 ease-out hover:-translate-y-0.5 hover:border-zinc-300/80 hover:shadow-[0_12px_40px_-12px_rgba(0,0,0,0.12)]',
])>
    @if ($variant === 'sale')
        <span class="absolute left-3 top-3 z-20 rounded-full bg-zinc-900 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-white shadow-sm">Sale</span>
    @endif
    @if ($variant === 'tab')
        <div class="relative aspect-[4/5] overflow-hidden bg-zinc-100">
            <a href="{{ route('products.show', $product) }}" class="relative z-0 block h-full w-full">
                @if ($img)
                    <img src="{{ $img->url() }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-700 ease-out group-hover:scale-[1.03]">
                @else
                    <div class="flex h-full min-h-[12rem] items-center justify-center text-sm text-zinc-400">No image</div>
                @endif
            </a>
            <div class="pointer-events-none absolute inset-x-0 top-0 h-20 bg-gradient-to-b from-black/[0.06] to-transparent opacity-60" aria-hidden="true"></div>
            {{-- Actions: hover reveal md+ only (hidden on mobile) --}}
            <div class="absolute inset-x-0 bottom-3 z-20 hidden justify-center px-2 opacity-0 translate-y-0 transition-all duration-300 md:flex md:opacity-0 md:translate-y-2 md:group-hover:translate-y-0 md:group-hover:opacity-100">
                <div class="pointer-events-auto flex items-center gap-0.5 rounded-full border border-zinc-200/90 bg-white/90 p-1 shadow-lg shadow-zinc-900/[0.08] backdrop-blur-md">
                    <button
                        type="button"
                        class="home-wishlist-btn flex h-9 w-9 items-center justify-center rounded-full text-zinc-600 transition hover:bg-zinc-100 hover:text-indigo-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/30"
                        data-wishlist-product-id="{{ $product->id }}"
                        data-in-wishlist="{{ $inWishlist ? '1' : '0' }}"
                        title="Wishlist"
                        aria-label="Add to wishlist"
                        aria-pressed="{{ $inWishlist ? 'true' : 'false' }}"
                    >
                        <svg class="h-[18px] w-[18px] home-wishlist-outline {{ $inWishlist ? 'hidden' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                        <svg class="h-[18px] w-[18px] home-wishlist-filled {{ $inWishlist ? '' : 'hidden' }}" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.003-.002.001h-.002z"/></svg>
                    </button>
                    <button
                        type="button"
                        class="home-quick-view-btn flex h-9 w-9 items-center justify-center rounded-full text-zinc-600 transition hover:bg-zinc-100 hover:text-indigo-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/30"
                        data-quick-view-url="{{ route('products.quick-view', $product) }}"
                        title="Quick view"
                        aria-label="Quick view"
                    >
                        <svg class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </button>
                    @if ($product->stock > 0)
                        <form action="{{ route('cart.add', $product->id) }}" method="post" class="store-add-cart-form inline">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button
                                type="submit"
                                class="home-add-cart-btn flex h-9 w-9 items-center justify-center rounded-full text-zinc-600 transition hover:bg-indigo-600 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/30"
                                title="Add to cart"
                                aria-label="Add to cart"
                            >
                                <svg class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                            </button>
                        </form>
                    @else
                        <span class="flex h-9 items-center rounded-full px-2 text-[9px] font-semibold uppercase tracking-wide text-zinc-400" title="Out of stock">Out</span>
                    @endif
                </div>
            </div>
        </div>
    @else
    <a href="{{ route('products.show', $product) }}" class="relative block aspect-[4/3] overflow-hidden bg-zinc-100">
        @if ($img)
            <img src="{{ $img->url() }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]">
        @else
            <div class="flex h-full items-center justify-center text-sm text-zinc-400">No image</div>
        @endif
    </a>
    @endif
    <div class="flex flex-1 flex-col p-3.5 pt-3 sm:p-4">
        @if ($variant === 'tab')
            <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-zinc-400">{{ $product->category->name }}</p>
            <h2 class="mt-1.5 line-clamp-2 text-sm font-semibold leading-snug tracking-tight text-zinc-900 sm:text-[15px]">
                <a href="{{ route('products.show', $product) }}" class="transition hover:text-indigo-600">{{ $product->name }}</a>
            </h2>
            <div class="mt-2 flex gap-0.5 text-amber-400" aria-hidden="true">
                @for ($i = 0; $i < 5; $i++)
                    <svg class="h-3.5 w-3.5 {{ $i < $stars ? 'text-amber-400' : 'text-zinc-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                @endfor
            </div>
            <div class="mt-auto pt-2.5">
                @if ($product->hasActiveDiscount())
                    <p class="text-xs tabular-nums text-zinc-400 line-through">{{ format_ghs($product->listPrice()) }}</p>
                @endif
                <p class="text-base font-bold tabular-nums tracking-tight text-zinc-900 sm:text-lg">{{ format_ghs($displayPrice) }}</p>
            </div>
        @else
            <div class="mt-0.5 flex justify-center gap-0.5 text-amber-400" aria-hidden="true">
                @for ($i = 0; $i < 5; $i++)
                    <svg class="h-3.5 w-3.5 {{ $i < $stars ? 'text-amber-400' : 'text-zinc-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                @endfor
            </div>
            <h2 class="mt-2 text-center text-sm font-semibold leading-snug tracking-tight text-zinc-900 sm:text-[15px]">
                <a href="{{ route('products.show', $product) }}" class="transition hover:text-indigo-600">{{ $product->name }}</a>
            </h2>
            <div class="mt-2 flex flex-wrap items-center justify-center gap-2">
                @if ($compareAt !== null)
                    <span class="text-xs tabular-nums text-zinc-400 line-through sm:text-sm">{{ format_ghs($compareAt) }}</span>
                @endif
                <span class="text-base font-bold tabular-nums text-rose-600 sm:text-lg">{{ format_ghs($displayPrice) }}</span>
            </div>
            @if ($product->stock > 0)
                <form action="{{ route('cart.add', $product->id) }}" method="post" class="store-add-cart-form mt-4">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="w-full rounded-xl bg-zinc-900 py-2.5 text-[11px] font-semibold uppercase tracking-[0.14em] text-white shadow-sm transition hover:bg-zinc-800">Add to cart</button>
                </form>
            @endif
        @endif
    </div>
</article>
@endif
