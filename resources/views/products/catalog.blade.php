@extends('layout')

@section('title', 'Products — ' . config('app.name'))
@section('main_class', 'w-full min-w-0 flex-1 bg-gray-50')

@section('content')
    @php
        $layout = $productDisplay->product_layout ?? 'grid';
        $currentCategory = $categorySlug !== '' ? $categories->firstWhere('slug', $categorySlug) : null;
        $filterBase = array_filter([
            'search' => $search !== '' ? $search : null,
            'sort' => $sort !== 'latest' ? $sort : null,
            'color' => $color !== '' ? $color : null,
            'min_price' => $minPrice !== null ? $minPrice : null,
            'max_price' => $maxPrice !== null ? $maxPrice : null,
        ], fn ($v) => $v !== null && $v !== '');
        if ($search !== '') {
            $catalogHeading = 'Search results';
            $catalogSub = 'Matches for your search. Use the top categories and right-side filters to refine.';
        } elseif ($currentCategory) {
            $catalogHeading = $currentCategory->name;
            $catalogSub = 'Products in this category. Adjust price, color, and sort from the right sidebar.';
        } else {
            $catalogHeading = 'Browse products';
            $catalogSub = 'Choose a category from the top bar, then refine with filters on the right.';
        }
        $hasCatalogRail = ($catalogQuickPicks ?? collect())->isNotEmpty();
    @endphp

    <div id="store-search" class="scroll-mt-24 pb-10 pt-5 sm:pb-12 sm:pt-7 lg:pb-14 lg:pt-8">
        <div class="store-box">
            <section class="mb-7 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm shadow-slate-200/40 sm:p-5" aria-label="Categories">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-sm font-semibold text-slate-900">Categories</h2>
                    @if ($currentCategory)
                        <a href="{{ route('products.index', $filterBase) }}" class="text-xs font-semibold text-[#0057b8] hover:text-[#00479a]">Clear category</a>
                    @endif
                </div>
                <div class="mt-3 flex snap-x snap-mandatory gap-2 overflow-x-auto pb-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                    <a
                        href="{{ route('products.index', $filterBase) }}"
                        class="snap-start whitespace-nowrap rounded-full border px-4 py-2 text-sm font-medium transition {{ $categorySlug === '' ? 'border-[#0057b8] bg-[#0057b8] text-white' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:text-[#0057b8]' }}"
                    >
                        All
                    </a>
                    @foreach ($categories as $cat)
                        <a
                            href="{{ route('shop.category', array_merge(['category' => $cat->slug], $filterBase)) }}"
                            class="snap-start whitespace-nowrap rounded-full border px-4 py-2 text-sm font-medium transition {{ $categorySlug === $cat->slug ? 'border-[#0057b8] bg-[#0057b8] text-white' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:text-[#0057b8]' }}"
                        >
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            </section>

            <div class="grid grid-cols-1 gap-8 xl:grid-cols-[1fr_minmax(280px,320px)] xl:items-start xl:gap-8">
                <div class="min-w-0 space-y-10">
                    <div class="max-w-2xl">
                        <h1 class="text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">{{ $catalogHeading }}</h1>
                        <p class="mt-1.5 text-sm leading-relaxed text-gray-500">{{ $catalogSub }}</p>
                        @if ($search !== '')
                            <p class="mt-2 text-sm text-slate-700">
                                <span class="font-medium">Searching for:</span>
                                <span class="text-slate-900">“{{ $search }}”</span>
                            </p>
                        @endif
                    </div>

                    @if ($categorySlug === '' && $featuredCarousel->isNotEmpty())
                        @include('products.partials.featured-products-section', [
                            'featuredProducts' => $featuredCarousel,
                            'productDisplay' => $productDisplay,
                            'featuredBare' => true,
                            'featuredCatalogCarousel' => true,
                            'featuredCatalogAutoplay' => true,
                        ])
                    @endif

                    <section aria-labelledby="all-products-heading">
                        <div class="mb-6 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                            <h2 id="all-products-heading" class="text-xl font-semibold text-gray-900">
                                @if ($search !== '')
                                    Matching products
                                @elseif ($currentCategory)
                                    {{ $currentCategory->name }}
                                @else
                                    All matching products
                                @endif
                            </h2>
                            <p class="text-sm text-gray-500">{{ $products->total() }} {{ $products->total() === 1 ? 'product' : 'products' }}</p>
                        </div>
                        @if ($products->isEmpty())
                            <div class="rounded-2xl border border-dashed border-gray-200 bg-white px-6 py-16 text-center text-gray-500">
                                No products match these filters. Try widening the price range or clearing a filter.
                            </div>
                        @else
                            @php
                                $gridClass = match ($layout) {
                                    'list' => 'flex flex-col gap-4',
                                    'carousel' => 'flex snap-x snap-mandatory gap-6 overflow-x-auto pb-4 store-scrollbar-none',
                                    'masonry' => 'columns-1 gap-6 space-y-6 sm:columns-2 lg:columns-3',
                                    'compact' => 'grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-3',
                                    'sleek' => 'grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3',
                                    default => 'grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3',
                                };
                            @endphp
                            <div class="{{ $gridClass }}">
                                @foreach ($products as $product)
                                    @if ($layout === 'masonry')
                                        <div class="break-inside-avoid">
                                            @include('products.partials.product-card', ['product' => $product, 'productDisplay' => $productDisplay, 'catalogPage' => true])
                                        </div>
                                    @elseif ($layout === 'carousel')
                                        <div class="w-[min(100%,280px)] shrink-0 snap-start sm:w-72">
                                            @include('products.partials.product-card', ['product' => $product, 'productDisplay' => $productDisplay, 'catalogPage' => true])
                                        </div>
                                    @else
                                        @include('products.partials.product-card', ['product' => $product, 'productDisplay' => $productDisplay, 'catalogPage' => true])
                                    @endif
                                @endforeach
                            </div>
                            <div class="mt-10">
                                {{ $products->links() }}
                            </div>
                        @endif
                    </section>
                </div>

                <aside class="space-y-4 xl:sticky xl:top-28 xl:self-start">
                    <form
                        method="get"
                        action="{{ route('products.index') }}"
                        class="catalog-filters-form"
                    >
                        <input type="hidden" name="category" value="{{ $categorySlug }}">
                        @include('products.partials.catalog-filters-sidebar')
                    </form>
                    @if ($hasCatalogRail)
                        <div>
                            @include('products.partials.catalog-right-rail')
                        </div>
                    @endif
                </aside>
            </div>
        </div>
    </div>
@endsection
