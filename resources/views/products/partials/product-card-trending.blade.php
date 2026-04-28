@php
    use App\Models\StoreProductDisplaySetting;

    $pd = $productDisplay ?? StoreProductDisplaySetting::current();
    $img = $product->images->first();
    $inStock = $product->stock > 0;
    $disc = $product->discountBadgeLabel();
    $isAdmin = auth()->check() && auth()->user()->role === 'admin';
@endphp
<article
    data-product-card
    class="group relative flex w-full flex-col overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-[0_2px_12px_rgba(15,23,42,0.07)] transition-shadow duration-300 hover:shadow-[0_4px_20px_rgba(15,23,42,0.1)]"
>
    @if ($disc && $inStock)
        <span class="pointer-events-none absolute left-2 top-2 z-20 flex h-11 w-11 items-center justify-center rounded-full bg-emerald-500 text-[10px] font-bold uppercase leading-tight text-white shadow-sm ring-2 ring-white">
            Sale
        </span>
    @endif

    @if (! $inStock)
        <span class="pointer-events-none absolute right-2 top-2 z-20 rounded-md bg-red-500 px-2 py-1 text-[10px] font-semibold uppercase tracking-wide text-white shadow-sm ring-2 ring-white">
            Sold out
        </span>
    @endif

    <div class="relative bg-white px-2 pt-3">
        <div class="relative flex h-[132px] items-center justify-center sm:h-[168px] md:h-[188px] lg:h-[200px]">
            @if ($inStock)
                <a href="{{ route('products.show', $product) }}" class="absolute inset-0 z-10 flex items-center justify-center p-3 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-pink-500/40" aria-label="{{ $product->name }}">
                    @if ($img)
                        <img
                            src="{{ $img->url() }}"
                            alt=""
                            class="max-h-full max-w-full object-contain"
                            loading="lazy"
                            decoding="async"
                        >
                    @else
                        <span class="text-sm text-slate-400">No image</span>
                    @endif
                </a>
            @else
                <div class="flex h-full w-full items-center justify-center p-3 opacity-70">
                    @if ($img)
                        <img src="{{ $img->url() }}" alt="" class="max-h-full max-w-full object-contain grayscale" loading="lazy">
                    @else
                        <span class="text-sm text-slate-400">No image</span>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex justify-center gap-0.5 pb-2 pt-1 text-slate-300" aria-hidden="true">
            @for ($i = 0; $i < 5; $i++)
                <i class="fa-solid fa-star text-[11px]"></i>
            @endfor
        </div>
    </div>

    <div class="flex flex-1 flex-col bg-slate-50/95 px-3 pb-4 pt-1 text-center">
        <a href="{{ route('products.show', $product) }}" class="block rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-500 focus-visible:ring-offset-2">
            <h3 class="line-clamp-2 min-h-0 text-[13px] font-medium leading-snug text-slate-800 sm:min-h-[2.5rem] sm:text-sm">{{ $product->name }}</h3>
            <div class="mt-2 flex flex-wrap items-center justify-center gap-x-2 gap-y-0.5">
                @if ($product->hasActiveDiscount())
                    <span class="text-sm tabular-nums text-slate-400 line-through">{{ format_ghs($product->listPrice()) }}</span>
                    <span class="text-lg font-bold tabular-nums text-pink-600">{{ format_ghs($product->effectivePrice()) }}</span>
                @else
                    <span class="text-lg font-bold tabular-nums text-pink-600">{{ format_ghs($product->price) }}</span>
                @endif
            </div>
        </a>
        @if ($isAdmin)
            <a
                href="{{ route('dashboard.products.edit', $product) }}"
                class="mt-2 inline-flex w-fit self-center items-center gap-1 rounded-md border border-blue-200 bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 transition hover:bg-blue-100"
            >
                Edit
            </a>
        @endif

    </div>
</article>
