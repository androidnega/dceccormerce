@php
    use App\Models\StoreProductDisplaySetting;

    $pd = $productDisplay ?? StoreProductDisplaySetting::current();
    $img = $product->images->first();
    $inStock = $product->stock > 0;
    $inWishlist = \App\Support\WishlistSession::has($product->id);
    $showCategory = $showCategory ?? false;
    $size = $pd->card_size;
    $hoverActions = $pd->enable_hover_actions;
    $hasDiscount = $product->hasActiveDiscount();
    $disc = $product->discountBadgeLabel();
    $stars = 4 + ($product->id % 2);

    $titleClass = match ($size) {
        StoreProductDisplaySetting::CARD_SMALL => 'text-[12px] sm:text-[13px]',
        StoreProductDisplaySetting::CARD_LARGE => 'text-sm sm:text-base',
        default => 'text-[13px] sm:text-[15px]',
    };
    $priceClass = match ($size) {
        StoreProductDisplaySetting::CARD_SMALL => 'text-sm sm:text-base',
        StoreProductDisplaySetting::CARD_LARGE => 'text-lg sm:text-xl',
        default => 'text-base sm:text-lg',
    };
@endphp
<article
    data-product-card
    data-product-card-sleek
    @class([
        'group relative flex h-full min-h-0 flex-col overflow-hidden rounded-xl border bg-white transition-[border-color,box-shadow] duration-300 max-sm:rounded-lg sm:min-h-[17rem] md:min-h-[19rem] lg:min-h-[20.5rem]',
        'border-[#E5E7EB] shadow-[0_1px_2px_rgba(0,0,0,0.04)] hover:border-[#C85045] hover:shadow-[0_4px_24px_-8px_rgba(200,80,69,0.15)]' => $hoverActions,
        'border-[#E5E7EB] shadow-[0_1px_2px_rgba(0,0,0,0.04)]' => ! $hoverActions,
    ])
>
    <div class="relative shrink-0 bg-white px-2.5 pb-0.5 pt-2 sm:px-5 sm:pb-1 sm:pt-5">
        @if ($disc && $inStock && $hasDiscount)
            <span
                @class([
                    'pointer-events-none absolute left-2.5 top-2.5 z-30 inline-flex rounded-md bg-[#C85045] px-1.5 py-0.5 text-[10px] font-bold tabular-nums text-white shadow-sm transition-opacity duration-300 sm:left-7 sm:top-7 sm:px-2 sm:py-1 sm:text-[11px]',
                    'opacity-0 max-md:opacity-100' => $hoverActions,
                    'group-hover:opacity-100' => $hoverActions,
                    'opacity-100' => ! $hoverActions,
                ])
            >{{ $disc }}</span>
        @endif

        @if (! $inStock)
            <div class="absolute inset-x-0 top-0 z-40 flex min-h-[6rem] items-center justify-center bg-white/90 backdrop-blur-[1px] sm:min-h-[10rem] md:min-h-[11rem]">
                <span class="text-xs font-medium text-neutral-600 sm:text-sm">Out of stock</span>
            </div>
        @endif

        <div class="relative mx-auto flex h-24 w-full max-h-[6rem] min-h-[5.75rem] items-center justify-center sm:h-[9.25rem] sm:min-h-0 sm:max-h-none md:h-[10.25rem] lg:h-44">
            @if ($inStock)
                <a href="{{ route('products.show', $product) }}" class="absolute inset-0 z-0 flex items-center justify-center focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-[#223A94]">
                    @if ($img)
                        <img
                            src="{{ $img->url() }}"
                            alt=""
                            class="max-h-full max-w-full object-contain object-center transition duration-300 ease-out group-hover:scale-[1.03]"
                            loading="lazy"
                            decoding="async"
                        >
                    @else
                        <span class="text-sm text-neutral-400">No image</span>
                    @endif
                </a>
            @else
                @if ($img)
                    <img src="{{ $img->url() }}" alt="" class="max-h-full max-w-full object-contain object-center opacity-50 grayscale" loading="lazy">
                @else
                    <span class="text-sm text-neutral-400">No image</span>
                @endif
            @endif
        </div>

        @if ($inStock && $hoverActions)
            <div
                class="relative z-20 -mt-2 hidden justify-center gap-1.5 pb-0.5 opacity-0 transition duration-300 ease-out pointer-events-none group-hover:pointer-events-auto group-hover:opacity-100 md:flex md:-mt-3 md:gap-2 md:pb-1"
            >
                @if ($pd->enable_wishlist)
                    <button
                        type="button"
                        class="store-wishlist-btn flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#223A94] text-white shadow-sm transition hover:bg-[#1a2d75] active:scale-95 sm:h-9 sm:w-9"
                        data-wishlist-product-id="{{ $product->id }}"
                        data-in-wishlist="{{ $inWishlist ? '1' : '0' }}"
                        title="Wishlist"
                        aria-label="Wishlist"
                        aria-pressed="{{ $inWishlist ? 'true' : 'false' }}"
                    >
                        <svg class="store-wishlist-outline h-4 w-4 {{ $inWishlist ? 'hidden' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                        <svg class="store-wishlist-filled h-4 w-4 text-white {{ $inWishlist ? '' : 'hidden' }}" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.003-.002.001h-.002z"/></svg>
                    </button>
                @endif
                <a
                    href="{{ route('products.show', $product) }}"
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#223A94] text-white shadow-sm transition hover:bg-[#1a2d75] active:scale-95 sm:h-9 sm:w-9"
                    title="Compare"
                    aria-label="View product to compare"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 8.25V6a2.25 2.25 0 00-2.25-2.25H6A2.25 2.25 0 003.75 6v8.25A2.25 2.25 0 006 16.5h2.25m7.5-8.25v2.25A2.25 2.25 0 0112 13.5H9.75m-6 0a2.25 2.25 0 00-2.25 2.25V18A2.25 2.25 0 006 20.25h8.25A2.25 2.25 0 0018 18v-9.75a2.25 2.25 0 00-2.25-2.25H9.75Z" />
                    </svg>
                </a>
                @if ($pd->enable_quick_view)
                    <button
                        type="button"
                        class="store-quick-view-btn flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#223A94] text-white shadow-sm transition hover:bg-[#1a2d75] active:scale-95 sm:h-9 sm:w-9"
                        data-quick-view-url="{{ route('products.quick-view', $product) }}"
                        title="Quick view"
                        aria-label="Quick view"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </button>
                @endif
            </div>
        @endif
    </div>

    <div class="flex flex-1 flex-col px-2.5 pb-2.5 pt-1 text-center sm:px-5 sm:pb-5 sm:pt-2">
        @if ($showCategory && $product->category)
            <p class="mb-1 text-[10px] font-medium uppercase tracking-wider text-neutral-400 sm:mb-1.5">{{ $product->category->name }}</p>
        @endif
        <a href="{{ route('products.show', $product) }}" class="block rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-[#223A94] focus-visible:ring-offset-2">
            <h3 class="min-h-0 line-clamp-2 font-semibold leading-snug tracking-tight text-neutral-900 max-sm:min-h-0 sm:min-h-[2.35rem] md:min-h-[2.6rem] {{ $titleClass }}">{{ $product->name }}</h3>
        </a>

        <div class="mt-1 flex justify-center gap-0.5 text-[#FBBF24] sm:mt-2" aria-hidden="true">
            @for ($i = 0; $i < 5; $i++)
                <svg class="h-2.5 w-2.5 sm:h-3.5 sm:w-3.5 {{ $i < $stars ? 'text-[#FBBF24]' : 'text-neutral-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            @endfor
        </div>

        <div class="mt-1 min-h-0 sm:mt-2 sm:min-h-[1.75rem]">
            @if ($hasDiscount && $inStock && $hoverActions)
                <div class="hidden max-md:flex flex-wrap items-center justify-center gap-x-2 gap-y-0.5">
                    <span class="{{ $priceClass }} font-bold tabular-nums text-[#D15147]">{{ format_ghs($product->effectivePrice()) }}</span>
                    <span class="text-sm tabular-nums text-[#9CA3AF] line-through">{{ format_ghs($product->listPrice()) }}</span>
                </div>
                <div class="hidden flex-wrap items-center justify-center gap-x-2 gap-y-0.5 md:flex md:group-hover:hidden max-md:hidden">
                    <span class="{{ $priceClass }} font-bold tabular-nums text-neutral-900">{{ format_ghs($product->effectivePrice()) }}</span>
                </div>
                <div class="hidden flex-wrap items-center justify-center gap-x-2 gap-y-0.5 md:group-hover:flex">
                    <span class="{{ $priceClass }} font-bold tabular-nums text-[#D15147]">{{ format_ghs($product->effectivePrice()) }}</span>
                    <span class="text-sm tabular-nums text-[#9CA3AF] line-through">{{ format_ghs($product->listPrice()) }}</span>
                </div>
            @elseif ($hasDiscount && $inStock)
                <div class="flex flex-wrap items-center justify-center gap-x-2 gap-y-0.5">
                    <span class="{{ $priceClass }} font-bold tabular-nums text-[#D15147]">{{ format_ghs($product->effectivePrice()) }}</span>
                    <span class="text-sm tabular-nums text-[#9CA3AF] line-through">{{ format_ghs($product->listPrice()) }}</span>
                </div>
            @else
                <span class="{{ $priceClass }} font-bold tabular-nums text-neutral-900">{{ format_ghs($product->price) }}</span>
            @endif
        </div>

        @if ($inStock)
            <form action="{{ route('cart.add', $product->id) }}" method="post" class="store-add-cart-form mt-auto pt-2 sm:pt-4" data-add-to-cart>
                @csrf
                <input type="hidden" name="quantity" value="1">
                <button
                    type="submit"
                    class="store-add-cart-btn w-full rounded-full bg-[#283C96] py-1.5 text-[12px] font-semibold text-white shadow-sm transition hover:bg-[#1f2d78] active:scale-[0.99] sm:py-3 sm:text-sm"
                    data-add-label="Add to cart"
                    data-added-label="Added"
                >
                    <span class="store-add-cart-label">Add to cart</span>
                </button>
            </form>
        @endif
    </div>
</article>
