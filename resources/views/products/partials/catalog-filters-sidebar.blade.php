@php
    $filterBase = array_filter([
        'search' => $search !== '' ? $search : null,
        'sort' => $sort !== 'latest' ? $sort : null,
        'color' => $color !== '' ? $color : null,
        'min_price' => $minPrice !== null ? $minPrice : null,
        'max_price' => $maxPrice !== null ? $maxPrice : null,
    ], fn ($v) => $v !== null && $v !== '');

    $label = 'text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500 lg:text-[11px]';
    $cardLg = 'lg:rounded-2xl lg:border lg:border-slate-200/90 lg:bg-white lg:p-5 lg:shadow-sm lg:shadow-slate-200/40';
@endphp

{{-- Compact right-rail filters: search, price, color, sort --}}
<div class="flex flex-col gap-4">
    <div class="flex flex-col overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-sm shadow-slate-200/40 lg:contents">
        <div class="divide-y divide-slate-100 lg:contents">
            {{-- Search --}}
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
            </div>

            {{-- Price --}}
            <div class="p-3 sm:p-4 {{ $cardLg }}">
                <p class="{{ $label }}">Price range</p>
                <div class="mt-2 lg:mt-3">
                    @include('products.partials.catalog-price-range')
                </div>
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
