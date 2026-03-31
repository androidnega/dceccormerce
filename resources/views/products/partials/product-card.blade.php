@php
    use App\Models\StoreProductDisplaySetting;

    $pd = $productDisplay ?? StoreProductDisplaySetting::current();
    $img = $product->images->first();
    $img2 = $product->images->skip(1)->first();
    $inStock = $product->stock > 0;
    $inWishlist = \App\Support\WishlistSession::has($product->id);
    $showCategory = $showCategory ?? false;
    $trendingStrip = $trendingStrip ?? false;
    $layout = ($forceGridLayout ?? false) ? StoreProductDisplaySetting::LAYOUT_GRID : $pd->product_layout;
    $size = $pd->card_size;
    $isList = $layout === StoreProductDisplaySetting::LAYOUT_LIST;
    $swap = $pd->enable_image_hover_swap && $img2 !== null && $inStock;

    $titleClass = match ($size) {
        StoreProductDisplaySetting::CARD_SMALL => 'text-xs',
        StoreProductDisplaySetting::CARD_LARGE => 'text-base',
        default => 'text-sm',
    };
    $priceClass = match ($size) {
        StoreProductDisplaySetting::CARD_SMALL => 'text-base',
        StoreProductDisplaySetting::CARD_LARGE => 'text-xl',
        default => 'text-lg',
    };
    $imgH = match ($size) {
        StoreProductDisplaySetting::CARD_SMALL => 'h-28 sm:h-32 md:h-36',
        StoreProductDisplaySetting::CARD_LARGE => 'h-36 sm:h-48 md:h-56',
        default => 'h-32 sm:h-40 md:h-48',
    };
@endphp
@if ($trendingStrip)
    @include('products.partials.product-card-trending', ['product' => $product, 'productDisplay' => $pd])
@elseif ($layout === StoreProductDisplaySetting::LAYOUT_LIST)
@php $disc = $product->discountBadgeLabel(); @endphp
<article
    data-product-card
    @class([
        'group relative flex w-full flex-col overflow-hidden rounded-2xl border',
        $trendingStrip
            ? 'border-black/[0.12] bg-[#fff9c4] shadow-none'
            : 'border-slate-200/90 bg-white shadow-sm transition duration-300 hover:shadow-sm',
        $isList ? 'sm:flex-row sm:items-stretch sm:gap-4' : '',
    ])
>
    <div @class([
        'relative w-full overflow-hidden rounded-2xl',
        $trendingStrip ? 'bg-[#fffde7]' : 'bg-slate-100',
        $isList ? 'sm:w-40 sm:max-w-[40%] sm:shrink-0' : '',
    ])>
        @if ($disc && $inStock)
            <span @class([
                'pointer-events-none absolute left-2 top-2 z-30 inline-flex rounded-md bg-rose-600 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-white',
                'shadow-sm' => ! $trendingStrip,
            ])>{{ $disc }}</span>
        @endif

        @if (! $inStock)
            <div class="absolute inset-0 z-40 flex items-center justify-center bg-white/85 backdrop-blur-[1px]">
                <span class="text-sm font-medium text-slate-600">Out of stock</span>
            </div>
        @endif

        <div class="relative flex w-full items-center justify-center p-2 sm:p-3 {{ $imgH }}">
            @if ($inStock)
                <a href="{{ route('products.show', $product) }}" class="absolute inset-0 z-0 flex items-center justify-center p-3 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-[#0057b8]">
                    @if ($swap)
                        <img
                            src="{{ $img->url() }}"
                            alt=""
                            class="max-h-full max-w-full object-contain transition-opacity duration-300 ease-out group-hover:opacity-0"
                            loading="lazy"
                            decoding="async"
                        >
                        <img
                            src="{{ $img2->url() }}"
                            alt=""
                            class="absolute inset-3 max-h-[calc(100%-1.5rem)] max-w-[calc(100%-1.5rem)] object-contain opacity-0 transition-opacity duration-300 ease-out group-hover:opacity-100"
                            loading="lazy"
                            decoding="async"
                        >
                    @elseif ($img)
                        <img
                            src="{{ $img->url() }}"
                            alt=""
                            @class([
                                'max-h-full max-w-full object-contain',
                                'transition duration-300 ease-out group-hover:scale-[1.04]' => ! $trendingStrip,
                            ])
                            loading="lazy"
                            decoding="async"
                        >
                    @else
                        <span class="text-sm text-slate-400">No image</span>
                    @endif
                </a>

                <div
                    class="absolute inset-0 z-20 hidden flex-col items-end justify-center gap-3 bg-black/0 p-4 opacity-0 transition duration-300 ease-out pointer-events-none md:flex md:group-hover:bg-black/35 md:group-hover:opacity-100"
                >
                    <div class="flex flex-col items-center justify-center gap-2 pointer-events-auto">
                        @if ($pd->enable_wishlist)
                            <button
                                type="button"
                                @class([
                                    'store-wishlist-btn flex h-9 w-9 items-center justify-center rounded-full bg-white text-slate-800 transition hover:scale-105 active:scale-95',
                                    'shadow-md' => ! $trendingStrip,
                                    'ring-1 ring-black/10' => $trendingStrip,
                                ])
                                data-wishlist-product-id="{{ $product->id }}"
                                data-in-wishlist="{{ $inWishlist ? '1' : '0' }}"
                                title="Wishlist"
                                aria-label="Wishlist"
                                aria-pressed="{{ $inWishlist ? 'true' : 'false' }}"
                            >
                                <svg class="store-wishlist-outline h-4 w-4 {{ $inWishlist ? 'hidden' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                                <svg class="store-wishlist-filled h-4 w-4 text-rose-600 {{ $inWishlist ? '' : 'hidden' }}" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.003-.002.001h-.002z"/></svg>
                            </button>
                        @endif
                        @if ($pd->enable_quick_view)
                            <button
                                type="button"
                                @class([
                                    'store-quick-view-btn flex h-9 w-9 items-center justify-center rounded-full bg-white text-slate-800 transition hover:scale-105 active:scale-95',
                                    'shadow-md' => ! $trendingStrip,
                                    'ring-1 ring-black/10' => $trendingStrip,
                                ])
                                data-quick-view-url="{{ route('products.quick-view', $product) }}"
                                title="Quick view"
                                aria-label="Quick view"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </button>
                        @endif
                    </div>
                    <form action="{{ route('cart.add', $product->id) }}" method="post" class="store-add-cart-form pointer-events-auto" data-add-to-cart>
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <button
                            type="submit"
                            @class([
                                'store-add-cart-btn flex h-9 w-9 items-center justify-center rounded-full bg-white text-slate-900 transition hover:bg-slate-50 active:scale-[0.98]',
                                'shadow-md' => ! $trendingStrip,
                                'ring-1 ring-black/10' => $trendingStrip,
                            ])
                            data-add-label="Add to cart"
                            data-added-label="Added"
                        >
                            <span class="hidden store-add-cart-label">Add to cart</span>
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M6 6h15l-1.5 9h-12z"></path>
                                <path d="M6 6l-2-2"></path>
                                <circle cx="9" cy="20" r="1"></circle>
                                <circle cx="18" cy="20" r="1"></circle>
                            </svg>
                        </button>
                    </form>
                </div>
            @else
                <div class="flex h-full w-full items-center justify-center p-3">
                    @if ($img)
                        <img src="{{ $img->url() }}" alt="" class="max-h-full max-w-full object-contain opacity-60 grayscale" loading="lazy">
                    @else
                        <span class="text-sm text-slate-400">No image</span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div @class([
        'mt-2 flex min-w-0 flex-1 flex-col px-3 pb-3 sm:mt-3 sm:px-4',
        $isList ? 'sm:mt-0 sm:justify-center' : '',
    ])>
        @if ($showCategory && $product->category)
            <p class="mb-2 text-[10px] font-medium uppercase tracking-wider text-slate-400">{{ $product->category->name }}</p>
        @endif
        <a href="{{ route('products.show', $product) }}" class="block rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0057b8] focus-visible:ring-offset-2">
            <h3 class="min-w-0 line-clamp-2 font-medium leading-snug text-slate-900 {{ $titleClass }}">{{ $product->name }}</h3>
            <div class="mt-1 flex w-full flex-wrap items-baseline gap-x-2 gap-y-0.5 min-w-0">
                @if ($product->hasActiveDiscount())
                    <span class="{{ $priceClass }} min-w-0 max-w-full truncate font-semibold tabular-nums text-slate-900">{{ format_ghs($product->effectivePrice()) }}</span>
                    <span class="min-w-0 max-w-full truncate text-sm tabular-nums text-slate-400 line-through">{{ format_ghs($product->listPrice()) }}</span>
                @else
                    <span class="{{ $priceClass }} min-w-0 max-w-full truncate font-semibold tabular-nums text-slate-900">{{ format_ghs($product->price) }}</span>
                @endif
            </div>
        </a>
        @if ($inStock)
            <form action="{{ route('cart.add', $product->id) }}" method="post" class="store-add-cart-form mt-1.5 md:hidden" data-add-to-cart>
                @csrf
                <input type="hidden" name="quantity" value="1">
                <button
                    type="submit"
                    class="store-add-cart-btn flex w-full items-center justify-center rounded-xl bg-slate-900 py-2 text-white transition hover:bg-slate-800 active:scale-[0.99] sm:py-2.5"
                    data-add-label="Add to cart"
                    data-added-label="Added"
                >
                    <span class="hidden store-add-cart-label">Add to cart</span>
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M6 6h15l-1.5 9h-12z"></path>
                        <path d="M6 6l-2-2"></path>
                        <circle cx="9" cy="20" r="1"></circle>
                        <circle cx="18" cy="20" r="1"></circle>
                    </svg>
                </button>
            </form>
        @endif
    </div>
</article>
@else
    @include('products.partials.product-card-sleek', ['product' => $product, 'productDisplay' => $pd, 'showCategory' => $showCategory ?? false])
@endif
