@php
    $filterBase = array_filter([
        'search' => $search !== '' ? $search : null,
        'sort' => $sort !== 'latest' ? $sort : null,
        'color' => $color !== '' ? $color : null,
        'min_price' => $minPrice !== null ? $minPrice : null,
        'max_price' => $maxPrice !== null ? $maxPrice : null,
    ], fn ($v) => $v !== null && $v !== '');

    $categoryPeek = 4;
    $cats = $categories->values();
    $visibleCats = $cats->take($categoryPeek);
    $moreCats = $cats->slice($categoryPeek);
    $moreCategoriesOpen = $categorySlug !== '' && $moreCats->contains(fn ($c) => $c->slug === $categorySlug);

    $label = 'text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500 lg:text-[11px]';
    $cardLg = 'lg:rounded-2xl lg:border lg:border-slate-200/90 lg:bg-white lg:p-5 lg:shadow-sm lg:shadow-slate-200/40';
@endphp

{{-- One form field set: mobile = single bordered panel + dividers; lg+ = three separate cards (lg:contents) --}}
<div class="flex flex-col gap-4">
    <div class="flex flex-col overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-sm shadow-slate-200/40 lg:contents">
        <div class="divide-y divide-slate-100 lg:contents">
            {{-- Price --}}
            <div class="p-3 sm:p-4 {{ $cardLg }}">
                <p class="{{ $label }}">Price range</p>
                <div class="mt-2 lg:mt-3">
                    @include('products.partials.catalog-price-range')
                </div>
            </div>

            {{-- Search + categories --}}
            <div class="p-3 sm:p-4 {{ $cardLg }}">
                <label for="catalog-search" class="{{ $label }}">Search</label>
                <div class="relative mt-1.5 lg:mt-2">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex w-8 items-center justify-center text-slate-400 lg:w-9" aria-hidden="true">
                        <svg class="h-3.5 w-3.5 lg:h-4 lg:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    </span>
                    <input
                        id="catalog-search"
                        name="search"
                        type="search"
                        value="{{ $search }}"
                        placeholder="Search products…"
                        autocomplete="off"
                        class="h-10 w-full min-h-0 rounded-lg border border-slate-200 bg-white py-0 pl-8 pr-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#0057b8] focus:outline-none focus:ring-2 focus:ring-[#0057b8]/20 sm:pl-9 sm:pr-3 lg:h-10 lg:rounded-xl lg:text-sm"
                    >
                </div>

                <p class="mb-1.5 mt-3 text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500 lg:mb-0 lg:mt-6 lg:text-[11px]">Categories</p>
                <ul class="mt-1.5 space-y-0 pr-0.5 lg:mt-2 lg:space-y-0.5">
                    <li>
                        <a
                            href="{{ route('products.index', array_filter(array_merge($filterBase, ['category' => null]))) }}"
                            class="flex min-h-0 items-center gap-2 rounded-lg px-2 py-2 text-[13px] font-semibold transition sm:gap-3 sm:rounded-xl sm:px-3 sm:py-2.5 sm:text-sm {{ $categorySlug === '' ? 'bg-[#0057b8] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-50 hover:text-[#0057b8]' }}"
                        >
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md sm:h-8 sm:w-8 sm:rounded-lg {{ $categorySlug === '' ? 'bg-white/15 text-white' : 'bg-slate-100 text-[#0057b8]' }}" aria-hidden="true"><i class="fa-solid fa-border-all"></i></span>
                            Browse all
                        </a>
                    </li>
                    @foreach ($visibleCats as $cat)
                        <li>
                            <a
                                href="{{ route('products.index', array_filter(array_merge($filterBase, ['category' => $cat->slug]))) }}"
                                class="flex min-h-0 items-center gap-2 rounded-lg px-2 py-2 text-[13px] font-medium transition sm:gap-3 sm:rounded-xl sm:px-3 sm:py-2.5 sm:text-sm {{ $categorySlug === $cat->slug ? 'bg-[#ffd700]/40 text-slate-900 ring-1 ring-slate-900/10' : 'text-slate-700 hover:bg-slate-50 hover:text-[#0057b8]' }}"
                            >
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-slate-100 text-[14px] text-[#0057b8] sm:h-8 sm:w-8 sm:rounded-lg sm:text-[15px] {{ $categorySlug === $cat->slug ? 'bg-white ring-1 ring-slate-200' : '' }}" aria-hidden="true"><i class="{{ category_fa_classes($cat->slug, $cat->name) }}"></i></span>
                                <span class="line-clamp-2">{{ $cat->name }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>

                @if ($moreCats->isNotEmpty())
                    <details class="catalog-categories-more mt-0.5 lg:mt-1" @if ($moreCategoriesOpen) open @endif>
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-2 rounded-lg px-2 py-2 text-[13px] font-semibold text-[#0057b8] transition hover:bg-slate-50 sm:rounded-xl sm:px-3 sm:py-2.5 sm:text-sm [&::-webkit-details-marker]:hidden">
                            <span>More categories</span>
                            <span class="text-[11px] font-medium text-slate-500 lg:text-xs">({{ $moreCats->count() }})</span>
                        </summary>
                        <ul class="mt-1 max-h-[min(32vh,200px)] space-y-0 overflow-y-auto border-t border-slate-100 pt-1.5 pr-0.5 lg:max-h-[min(40vh,260px)] lg:space-y-0.5 lg:pt-2">
                            @foreach ($moreCats as $cat)
                                <li>
                                    <a
                                        href="{{ route('products.index', array_filter(array_merge($filterBase, ['category' => $cat->slug]))) }}"
                                        class="flex min-h-0 items-center gap-2 rounded-lg px-2 py-2 text-[13px] font-medium transition sm:gap-3 sm:rounded-xl sm:px-3 sm:py-2.5 sm:text-sm {{ $categorySlug === $cat->slug ? 'bg-[#ffd700]/40 text-slate-900 ring-1 ring-slate-900/10' : 'text-slate-700 hover:bg-slate-50 hover:text-[#0057b8]' }}"
                                    >
                                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-slate-100 text-[14px] text-[#0057b8] sm:h-8 sm:w-8 sm:rounded-lg sm:text-[15px] {{ $categorySlug === $cat->slug ? 'bg-white ring-1 ring-slate-200' : '' }}" aria-hidden="true"><i class="{{ category_fa_classes($cat->slug, $cat->name) }}"></i></span>
                                        <span class="line-clamp-2">{{ $cat->name }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </details>
                @endif
            </div>

            {{-- Color, sort, actions --}}
            <div class="p-3 sm:p-4 {{ $cardLg }}">
                <div class="grid grid-cols-2 gap-x-2 gap-y-2 lg:grid-cols-1 lg:gap-y-0">
                    <div class="min-w-0">
                        <label for="catalog-color" class="{{ $label }}">Color</label>
                        <select
                            id="catalog-color"
                            name="color"
                            class="mt-1.5 h-10 w-full min-h-0 rounded-lg border border-slate-200 bg-white px-2 text-sm text-slate-900 focus:border-[#0057b8] focus:outline-none focus:ring-2 focus:ring-[#0057b8]/20 lg:mt-2 lg:rounded-xl lg:px-3"
                            aria-label="Filter by color"
                        >
                            <option value="">Any</option>
                            @foreach ($colors as $c)
                                <option value="{{ $c }}" @selected($color === $c)>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-0 lg:mt-5">
                        <label for="catalog-sort" class="{{ $label }}">Sort</label>
                        <select
                            id="catalog-sort"
                            name="sort"
                            class="mt-1.5 h-10 w-full min-h-0 rounded-lg border border-slate-200 bg-white px-2 text-sm text-slate-900 focus:border-[#0057b8] focus:outline-none focus:ring-2 focus:ring-[#0057b8]/20 lg:mt-2 lg:rounded-xl lg:px-3"
                            aria-label="Sort products"
                        >
                            <option value="latest" @selected($sort === 'latest')>Latest</option>
                            <option value="price_low" @selected($sort === 'price_low')>Price: low to high</option>
                            <option value="price_high" @selected($sort === 'price_high')>Price: high to low</option>
                            <option value="name_az" @selected($sort === 'name_az')>Name A–Z</option>
                            <option value="name_za" @selected($sort === 'name_za')>Name Z–A</option>
                        </select>
                    </div>
                </div>

                <div class="mt-3 grid grid-cols-2 gap-2 lg:mt-6 lg:flex lg:flex-row lg:gap-2">
                    <button type="submit" class="h-10 rounded-lg bg-[#0057b8] px-3 text-xs font-semibold text-white transition hover:bg-[#00479a] sm:text-sm lg:h-10 lg:flex-1 lg:rounded-xl lg:text-sm">
                        <span class="lg:hidden">Apply</span>
                        <span class="hidden lg:inline">Apply filters</span>
                    </button>
                    <a href="{{ route('products.index') }}" class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-200 px-3 text-xs font-medium text-slate-600 transition hover:bg-slate-50 sm:text-sm lg:h-10 lg:flex-1 lg:rounded-xl lg:text-sm">
                        <span class="lg:hidden">Clear</span>
                        <span class="hidden lg:inline">Clear all</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
