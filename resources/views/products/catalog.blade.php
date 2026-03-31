@extends('layout')

@section('title', 'Products — ' . config('app.name'))
@section('main_class', 'w-full flex-1 bg-gray-50')

@section('content')
    @php
        $layout = $productDisplay->product_layout ?? 'grid';
        $currentCategory = $categorySlug !== '' ? $categories->firstWhere('slug', $categorySlug) : null;
        if ($search !== '') {
            $catalogHeading = 'Search results';
            $catalogSub = 'Matches for your search — refine with categories and filters in the sidebar.';
        } elseif ($currentCategory) {
            $catalogHeading = $currentCategory->name;
            $catalogSub = 'Products in this category. Adjust price, color, and sort from the sidebar.';
        } else {
            $catalogHeading = 'Browse products';
            $catalogSub = 'Search or pick a category — use the sidebar for price range, color, and sort.';
        }
        $hasCatalogRail = ($catalogQuickPicks ?? collect())->isNotEmpty();
    @endphp

    <div id="store-search" class="scroll-mt-24 pb-10 pt-5 sm:pb-12 sm:pt-7 lg:pb-14 lg:pt-8">
        <div class="store-box">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-[minmax(260px,280px)_1fr] lg:items-start lg:gap-10">
                <form
                    method="get"
                    action="{{ route('products.index') }}"
                    class="catalog-filters-form lg:sticky lg:top-28 lg:self-start"
                >
                    <input type="hidden" name="category" value="{{ $categorySlug }}">
                    @include('products.partials.catalog-filters-sidebar')
                </form>

                <div class="min-w-0">
                    <div @class([
                        'grid grid-cols-1 gap-8',
                        'xl:grid-cols-[1fr_minmax(240px,280px)] xl:items-start xl:gap-8' => $hasCatalogRail,
                    ])>
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
                                                    @include('products.partials.product-card', ['product' => $product, 'productDisplay' => $productDisplay])
                                                </div>
                                            @elseif ($layout === 'carousel')
                                                <div class="w-[min(100%,280px)] shrink-0 snap-start sm:w-72">
                                                    @include('products.partials.product-card', ['product' => $product, 'productDisplay' => $productDisplay])
                                                </div>
                                            @else
                                                @include('products.partials.product-card', ['product' => $product, 'productDisplay' => $productDisplay])
                                            @endif
                                        @endforeach
                                    </div>
                                    <div class="mt-10">
                                        {{ $products->links() }}
                                    </div>
                                @endif
                            </section>
                        </div>

                        @if ($hasCatalogRail)
                            <div class="xl:sticky xl:top-28 xl:self-start">
                                @include('products.partials.catalog-right-rail')
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
