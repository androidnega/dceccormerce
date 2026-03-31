@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\Product>|null $catalogQuickPicks */
    $picks = $catalogQuickPicks ?? collect();
    $railCategory = $currentCategory ?? null;
@endphp
@if ($picks->isNotEmpty())
    <aside
        class="rounded-2xl border border-slate-200/90 bg-white p-4 shadow-sm shadow-slate-200/40 lg:p-5"
        aria-labelledby="catalog-rail-heading"
    >
        <h2 id="catalog-rail-heading" class="text-[11px] font-bold uppercase tracking-[0.14em] text-slate-500">
            @if ($railCategory)
                {{ $railCategory->name }} — picks
            @else
                Fresh picks
            @endif
        </h2>
        <p class="mt-1 text-xs leading-snug text-slate-500">
            @if ($railCategory)
                Popular in this category — tap for details.
            @else
                Hand-picked highlights — tap to view details.
            @endif
        </p>
        <ul class="mt-4 space-y-1">
            @foreach ($picks->values() as $index => $product)
                @php
                    $img = $product->images->first();
                    $imgUrl = $img ? $img->url() : null;
                @endphp
                <li @class(['max-xl:hidden' => $index >= 2])>
                    <a
                        href="{{ route('products.show', $product) }}"
                        class="group flex gap-3 rounded-xl p-2 transition-colors hover:bg-slate-50"
                    >
                        <div class="relative h-[4.5rem] w-[4.5rem] shrink-0 overflow-hidden rounded-xl border border-slate-100 bg-slate-100">
                            @if ($imgUrl)
                                <img
                                    src="{{ $imgUrl }}"
                                    alt=""
                                    class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.04]"
                                    loading="lazy"
                                    decoding="async"
                                >
                            @else
                                <div class="flex h-full w-full items-center justify-center text-slate-300" aria-hidden="true">
                                    <i class="fa-solid fa-mobile-screen text-xl"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex min-w-0 flex-1 flex-col justify-center gap-0.5">
                            <p class="line-clamp-2 text-[13px] font-semibold leading-snug text-slate-900 group-hover:text-[#0057b8]">
                                {{ $product->name }}
                            </p>
                            @if ($product->category)
                                <p class="truncate text-[11px] font-medium uppercase tracking-wide text-slate-400">{{ $product->category->name }}</p>
                            @endif
                            <p class="text-sm font-bold tabular-nums text-slate-800">{{ format_ghs($product->price) }}</p>
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    </aside>
@endif
