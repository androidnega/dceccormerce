<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\HeroSlide;
use App\Models\HomepageSection;
use App\Models\HomepageSetting;
use App\Models\NewsPost;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductRating;
use App\Models\Promo;
use App\Models\SaleSpotlightCard;
use App\Models\StoreProductDisplaySetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $search = trim((string) $request->query('search', ''));
        $categorySlug = trim((string) $request->query('category', ''));

        if ($search !== '' || $categorySlug !== '') {
            return redirect()->route('products.index', $request->query());
        }

        $categories = Category::query()->orderBy('name')->get();

        $showHighlights = true;

        $homepageSettings = HomepageSetting::current();
        $homepageLayout = $homepageSettings->homepage_layout;
        $useSidebarHomeLayout = $showHighlights && $homepageLayout === HomepageSetting::LAYOUT_SIDEBAR;
        $useStackedCardsLayout = $showHighlights && $homepageLayout === HomepageSetting::LAYOUT_STACKED_CARDS;

        $heroSlides = $showHighlights ? $this->heroSlidesForView() : [];

        $sidebarCategories = $useSidebarHomeLayout
            ? $homepageSettings->resolvedSidebarCategories()
            : collect();

        $productDisplay = StoreProductDisplaySetting::current();

        $newsPosts = $showHighlights
            ? NewsPost::query()->active()->ordered()->limit(9)->get()
            : collect();

        $homepageSections = $showHighlights
            ? HomepageSection::query()->active()->ordered()->get()
            : collect();

        $saleSpotlightItems = $showHighlights
            ? $this->saleSpotlightItemsForHome()
            : [];

        $homepageSectionProducts = [];
        if ($showHighlights && $homepageSections->isNotEmpty()) {
            foreach ($homepageSections as $section) {
                if (in_array($section->type, [HomepageSection::TYPE_PRODUCT_GRID, HomepageSection::TYPE_FLASH_SECTION], true)) {
                    $homepageSectionProducts[$section->id] = $this->productsForHomepageSection($section);
                }
            }
        }

        return view('products.index', [
            'categories' => $categories,
            'productDisplay' => $productDisplay,
            'search' => '',
            'categorySlug' => '',
            'showHighlights' => $showHighlights,
            'heroSlides' => $heroSlides,
            'homepageLayout' => $homepageLayout,
            'homepageSettings' => $homepageSettings,
            'useSidebarHomeLayout' => $useSidebarHomeLayout,
            'useStackedCardsLayout' => $useStackedCardsLayout,
            'sidebarCategories' => $sidebarCategories,
            'newsPosts' => $newsPosts,
            'homepageSections' => $homepageSections,
            'homepageSectionProducts' => $homepageSectionProducts,
            'saleSpotlightItems' => $saleSpotlightItems,
        ]);
    }

    /**
     * Up to three products for the home “On sale” row: discounted / flash first, then latest to fill.
     *
     * @return Collection<int, Product>
     */
    private function saleSpotlightProductsForHome(int $limit = 3, ?Collection $excludeIds = null): Collection
    {
        $excludeIds = $excludeIds ?? collect();

        $onSale = Product::query()
            ->active()
            ->with(['category', 'images'])
            ->where(function ($q) {
                $q->whereNotNull('discount_type')->where('discount_type', '!=', '')
                    ->orWhere('flash_sale', true);
            })
            ->when($excludeIds->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $excludeIds))
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        if ($onSale->count() >= $limit) {
            return $onSale;
        }

        $ids = $onSale->pluck('id');
        $needed = $limit - $onSale->count();
        $excludeAll = $excludeIds->merge($ids)->unique();
        $fill = Product::query()
            ->active()
            ->with(['category', 'images'])
            ->when($excludeAll->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $excludeAll))
            ->orderByDesc('id')
            ->limit($needed)
            ->get();

        return $onSale->concat($fill);
    }

    /**
     * @return array<int, array{product: Product, imageUrl: ?string}>
     */
    private function saleSpotlightItemsForHome(): array
    {
        $limit = 3;

        $cards = SaleSpotlightCard::query()
            ->where('is_active', true)
            ->whereHas('product', fn ($q) => $q->active())
            ->orderBy('position')
            ->limit($limit)
            ->with([
                'product' => fn ($q) => $q->active()->with(['category', 'images']),
            ])
            ->get();

        $items = [];
        $usedProductIds = collect();

        foreach ($cards as $card) {
            if ($card->product === null) {
                continue;
            }

            $items[] = [
                'product' => $card->product,
                'imageUrl' => $card->imageUrl(),
            ];
            $usedProductIds->push($card->product->id);
        }

        // If the admin has enabled at least one spotlight card, show ONLY those.
        // Do not auto-fill inactive slots.
        if (count($items) > 0) {
            return $items;
        }

        // If none are active, fall back to regular on-sale products so the section isn't empty.
        $fallbackProducts = $this->saleSpotlightProductsForHome($limit, $usedProductIds);

        return $fallbackProducts->map(function (Product $p) {
            return [
                'product' => $p,
                'imageUrl' => null,
            ];
        })->values()->all();
    }

    /**
     * @return Collection<int, Product>
     */
    private function productsForHomepageSection(HomepageSection $section): Collection
    {
        $defaultLimit = $section->type === HomepageSection::TYPE_FLASH_SECTION ? 4 : 8;
        $limit = (int) ($section->config['limit'] ?? $defaultLimit);
        $limit = max(1, min(24, $limit));
        $source = $section->config['source'] ?? 'latest';

        $q = Product::query()->active()->with(['category', 'images']);

        return match ($source) {
            'featured' => $q->orderByDesc('is_featured')->orderByDesc('id')->limit($limit)->get(),
            'sale' => $q->where(function ($q2) {
                $q2->whereNotNull('discount_type')->where('discount_type', '!=', '')
                    ->orWhere('flash_sale', true);
            })->orderByDesc('id')->limit($limit)->get(),
            'trending' => $q->orderByDesc('is_trending')->orderByDesc('id')->limit($limit)->get(),
            default => $q->orderByDesc('id')->limit($limit)->get(),
        };
    }

    public function catalog(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $categorySlug = trim((string) $request->query('category', ''));
        $color = trim((string) $request->query('color', ''));
        $sort = trim((string) $request->query('sort', 'latest'));
        $minPrice = is_numeric($request->query('min_price')) ? (float) $request->query('min_price') : null;
        $maxPrice = is_numeric($request->query('max_price')) ? (float) $request->query('max_price') : null;

        $applyCatalogFilters = function ($query) use ($search, $categorySlug, $color): void {
            if ($search !== '') {
                $escaped = addcslashes($search, '%_\\');
                $query->where('name', 'like', '%'.$escaped.'%');
            }

            if ($categorySlug !== '') {
                $query->whereHas('category', function ($q) use ($categorySlug) {
                    $q->where('slug', $categorySlug);
                });
            }

            if ($color !== '') {
                $query->where('name', 'like', '%'.$color.'%');
            }
        };

        $baseQuery = Product::query()
            ->active()
            ->with(['category', 'images'])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings');
        $applyCatalogFilters($baseQuery);

        $boundsQuery = Product::query()->active();
        $applyCatalogFilters($boundsQuery);
        $boundsRow = $boundsQuery->selectRaw('MIN(price) as min_price, MAX(price) as max_price')->first();
        $rawMin = $boundsRow?->min_price;
        $rawMax = $boundsRow?->max_price;

        // When the current filters match no rows, MIN/MAX are null — fall back to store-wide
        // active product bounds so the price slider always works.
        if ($rawMin === null || $rawMax === null) {
            $storeBounds = Product::query()->active()->selectRaw('MIN(price) as min_price, MAX(price) as max_price')->first();
            $rawMin = $storeBounds?->min_price;
            $rawMax = $storeBounds?->max_price;
        }

        if ($rawMin === null || $rawMax === null) {
            $catalogMinPrice = 0.0;
            $catalogMaxPrice = 10_000.0;
        } else {
            $catalogMinPrice = (float) $rawMin;
            $catalogMaxPrice = (float) $rawMax;
            if ($catalogMaxPrice <= $catalogMinPrice) {
                $catalogMaxPrice = $catalogMinPrice + 1.0;
            }
        }

        $sliderMin = (int) floor($catalogMinPrice);
        $sliderMax = (int) ceil($catalogMaxPrice);
        if ($sliderMax <= $sliderMin) {
            $sliderMax = $sliderMin + 1;
        }

        $span = $sliderMax - $sliderMin;
        $priceSliderStep = $span > 10_000 ? 500 : ($span > 2500 ? 100 : ($span > 500 ? 25 : ($span > 100 ? 5 : 1)));

        if ($minPrice !== null && $minPrice <= $catalogMinPrice) {
            $minPrice = null;
        }
        if ($maxPrice !== null && $maxPrice >= $catalogMaxPrice) {
            $maxPrice = null;
        }

        $query = clone $baseQuery;
        if ($minPrice !== null) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice !== null) {
            $query->where('price', '<=', $maxPrice);
        }

        $sortMap = [
            'latest' => ['id', 'desc'],
            'price_low' => ['price', 'asc'],
            'price_high' => ['price', 'desc'],
            'name_az' => ['name', 'asc'],
            'name_za' => ['name', 'desc'],
        ];
        [$sortColumn, $sortDir] = $sortMap[$sort] ?? $sortMap['latest'];
        $query->orderBy($sortColumn, $sortDir);

        $categories = Category::query()->orderBy('name')->get();
        $products = $query->paginate(16)->withQueryString();

        $featuredCarousel = $categorySlug === ''
            ? $this->catalogContextProducts('', 10)
            : collect();
        $catalogQuickPicks = $this->catalogContextProducts($categorySlug, 4);

        $colors = ['Black', 'White', 'Blue', 'Red', 'Green', 'Silver', 'Gold', 'Purple'];

        return view('products.catalog', [
            'products' => $products,
            'categories' => $categories,
            'search' => $search,
            'categorySlug' => $categorySlug,
            'color' => $color,
            'sort' => $sort,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'catalogMinPrice' => $catalogMinPrice,
            'catalogMaxPrice' => $catalogMaxPrice,
            'sliderMin' => $sliderMin,
            'sliderMax' => $sliderMax,
            'priceSliderStep' => $priceSliderStep,
            'colors' => $colors,
            'featuredCarousel' => $featuredCarousel,
            'catalogQuickPicks' => $catalogQuickPicks,
            'productDisplay' => StoreProductDisplaySetting::current(),
        ]);
    }

    public function category(Request $request, Category $category): View
    {
        $query = array_merge($request->query(), ['category' => $category->slug]);
        $categoryRequest = $request->duplicate($query);

        return $this->catalog($categoryRequest);
    }

    /**
     * Featured strip and Fresh picks: prefer products in the current category when filtered.
     * Falls back to store-wide when the category has no matches.
     *
     * @return Collection<int, Product>
     */
    private function catalogContextProducts(?string $categorySlug, int $limit): Collection
    {
        $q = Product::query()
            ->active()
            ->with(['category', 'images'])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings');
        if ($categorySlug !== '') {
            $q->whereHas('category', function ($q2) use ($categorySlug) {
                $q2->where('slug', $categorySlug);
            });
        }
        $list = $q->latest()->limit($limit)->get();
        if ($list->isEmpty() && $categorySlug !== '') {
            return Product::query()
                ->active()
                ->with(['category', 'images'])
                ->withAvg('ratings', 'rating')
                ->withCount('ratings')
                ->latest()
                ->limit($limit)
                ->get();
        }

        return $list;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function heroSlidesForView(): array
    {
        $dbSlides = HeroSlide::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->with(['product.images'])
            ->get();

        if ($dbSlides->isNotEmpty()) {
            return $dbSlides->map(fn (HeroSlide $slide) => $this->mapHeroSlide($slide))->all();
        }

        return $this->legacyHeroSlides();
    }

    /**
     * Split hero (headline left, product art right). Image is not used as a full-bleed background so it stays contained and responsive.
     *
     * @return array<string, mixed>
     */
    private function mapHeroSlide(HeroSlide $slide): array
    {
        $product = $slide->product;
        $imageUrl = $slide->imageUrl();

        if (($imageUrl === null || $imageUrl === '') && $product !== null) {
            $imageUrl = (string) ($product->images->first()?->url() ?? '');
        }

        $imageStr = $imageUrl ?? '';

        $row = [
            'sub' => $slide->subheading,
            'headline' => $slide->headline,
            'image' => $imageStr,
            'image_alt' => $slide->headline,
            'cta_label' => $slide->cta_label !== '' ? $slide->cta_label : 'Shop now',
            'cta_href' => $slide->resolveCtaUrl(),
            'product' => $product,
            'product_tag' => $this->heroProductTag($product),
        ];

        if ($slide->headline_line2 !== null && $slide->headline_line2 !== '') {
            $row['headline_lines'] = [$slide->headline, $slide->headline_line2];
        }

        return $row;
    }

    /**
     * Small badge for stacked-card hero when a slide is tied to a product (NEW = recently created, else HOT).
     */
    private function heroProductTag(?Product $product): ?string
    {
        if ($product === null) {
            return null;
        }

        if ($product->created_at !== null && $product->created_at->greaterThan(now()->subDays(30))) {
            return 'NEW';
        }

        return 'HOT';
    }

    /**
     * Default hero when no admin {@see HeroSlide} rows exist: full-bleed art + left-aligned copy (full-width layout).
     *
     * @return list<array<string, mixed>>
     */
    private function legacyHeroSlides(): array
    {
        $shop = route('products.index').'#store-search';

        return [
            [
                'product' => null,
                'product_tag' => null,
                'sub' => 'Smart activity tracker',
                'headline' => 'iWatch 42 Sport Black',
                'headline_lines' => ['iWatch 42 Sport Black'],
                'image' => '/images/hero-watch-showcase.png',
                'image_alt' => 'Apple Watch',
                'cta_label' => 'Read more',
                'cta_href' => $shop,
            ],
            [
                'product' => null,
                'product_tag' => null,
                'sub' => 'Pro. Beyond pro.',
                'headline' => 'iPhone — your next upgrade',
                'headline_lines' => ['iPhone', 'Your next upgrade'],
                'image' => '/images/apple-iphone-14-product-red-guenstig-gebraucht-kaufen.webp',
                'image_alt' => 'iPhone',
                'cta_label' => 'Read more',
                'cta_href' => $shop,
            ],
            [
                'product' => null,
                'product_tag' => null,
                'sub' => 'All new 13-inch & 15-inch',
                'headline' => 'MacBook with retina display',
                'headline_lines' => ['MacBook with', 'retina display'],
                'image' => '/images/ss1_copy_1920x.webp',
                'image_alt' => 'MacBook',
                'cta_label' => 'Read more',
                'cta_href' => $shop,
            ],
        ];
    }

    public function show(Request $request, Product $product): View
    {
        abort_unless($product->is_active, 404);

        $product->load([
            'category',
            'images',
            'ratings' => fn ($q) => $q->with('user')->latest()->limit(20),
        ])->loadAvg('ratings', 'rating')->loadCount('ratings');

        $relatedProducts = Product::query()
            ->active()
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->getKey())
            ->with(['category', 'images'])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->latest()
            ->limit(4)
            ->get();

        $ratingCounts = ProductRating::query()
            ->where('product_id', $product->id)
            ->selectRaw('rating, COUNT(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating');
        $totalRatings = (int) $ratingCounts->sum();
        $ratingBreakdown = collect(range(5, 1))->map(function (int $star) use ($ratingCounts, $totalRatings) {
            $count = (int) ($ratingCounts[$star] ?? 0);
            $percent = $totalRatings > 0 ? (int) round(($count / $totalRatings) * 100) : 0;

            return [
                'star' => $star,
                'count' => $count,
                'percent' => $percent,
            ];
        });

        $activeDiscountPromo = Promo::query()
            ->active()
            ->where('type', Promo::TYPE_DISCOUNT)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        $alsoBoughtIds = OrderItem::query()
            ->whereIn('order_id', function ($q) use ($product) {
                $q->from('order_items')
                    ->select('order_id')
                    ->where('product_id', $product->id);
            })
            ->where('product_id', '!=', $product->id)
            ->selectRaw('product_id, SUM(quantity) as qty')
            ->groupBy('product_id')
            ->orderByDesc('qty')
            ->limit(8)
            ->pluck('product_id')
            ->all();

        $alsoBoughtProducts = collect();
        if (count($alsoBoughtIds) > 0) {
            $alsoBoughtProducts = Product::query()
                ->active()
                ->whereIn('id', $alsoBoughtIds)
                ->with(['category', 'images'])
                ->withAvg('ratings', 'rating')
                ->withCount('ratings')
                ->get()
                ->sortBy(fn (Product $p) => array_search($p->id, $alsoBoughtIds, true))
                ->take(4)
                ->values();
        }

        $recentIds = collect((array) $request->session()->get('recently_viewed_products', []))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->prepend($product->id)
            ->unique()
            ->take(12)
            ->values();
        $request->session()->put('recently_viewed_products', $recentIds->all());

        $recentlyViewedProducts = Product::query()
            ->active()
            ->whereIn('id', $recentIds->reject(fn ($id) => $id === $product->id)->take(4)->all())
            ->with(['category', 'images'])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->latest()
            ->get();

        $watchingNow = $this->trackLiveViewers($request, $product);

        $userRating = auth()->check()
            ? $product->ratings()->where('user_id', auth()->id())->first()
            : null;

        return view('products.show', compact(
            'product',
            'relatedProducts',
            'userRating',
            'ratingBreakdown',
            'activeDiscountPromo',
            'alsoBoughtProducts',
            'recentlyViewedProducts',
            'watchingNow'
        ));
    }

    private function trackLiveViewers(Request $request, Product $product): int
    {
        $cacheKey = 'product_viewers:'.$product->id;
        $viewerKey = (string) ($request->session()->getId() ?: $request->ip() ?: uniqid('viewer_', true));
        $now = time();
        $ttlSeconds = 300;

        $viewers = Cache::get($cacheKey, []);
        if (! is_array($viewers)) {
            $viewers = [];
        }

        $viewers[$viewerKey] = $now;
        $cutoff = $now - $ttlSeconds;
        $viewers = array_filter($viewers, fn ($seenAt) => is_numeric($seenAt) && (int) $seenAt >= $cutoff);

        Cache::put($cacheKey, $viewers, now()->addSeconds($ttlSeconds));

        return count($viewers);
    }

    public function watchers(Request $request, Product $product): JsonResponse
    {
        abort_unless($product->is_active, 404);

        $count = $this->trackLiveViewers($request, $product);

        return response()->json([
            'count' => $count,
            'label' => $count.' '.($count === 1 ? 'person is' : 'people are').' viewing this right now.',
        ]);
    }

    public function rate(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->is_active, 404);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'max:1000'],
        ]);

        ProductRating::query()->updateOrCreate(
            [
                'product_id' => $product->id,
                'user_id' => (int) $request->user()->id,
            ],
            [
                'rating' => (int) $validated['rating'],
                'review' => trim((string) ($validated['review'] ?? '')) ?: null,
            ]
        );

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Your rating has been saved.');
    }

    public function imageOpen(Product $product, ProductImage $productImage): View
    {
        abort_unless($product->is_active, 404);
        abort_if($productImage->product_id !== $product->id, 404);

        return view('products.image-open', [
            'product' => $product,
            'image' => $productImage,
        ]);
    }

    public function quickView(Product $product): JsonResponse
    {
        abort_unless($product->is_active, 404);

        $product->load(['category', 'images']);

        $rawDescription = (string) ($product->description ?? '');
        $descriptionPlain = trim(preg_replace('/\s+/u', ' ', strip_tags($rawDescription)));

        $primaryImage = $product->images->first();
        $specs = [
            ['label' => 'Category', 'value' => $product->category->name],
            ['label' => 'Stock', 'value' => (string) $product->stock],
            ['label' => 'Status', 'value' => $product->stock > 0 ? 'In stock' : 'Out of stock'],
            ['label' => 'Code', 'value' => strtoupper((string) $product->slug)],
        ];

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => (string) $product->effectivePrice(),
            'priceFormatted' => format_ghs($product->effectivePrice()),
            'listPriceFormatted' => $product->hasActiveDiscount() ? format_ghs($product->listPrice()) : null,
            'discountBadge' => $product->discountBadgeLabel(),
            'category' => $product->category->name,
            'stock' => $product->stock,
            'inStock' => $product->stock > 0,
            'description' => $descriptionPlain,
            'mainImage' => $primaryImage?->url(),
            'specs' => $specs,
            'images' => $product->images->map(fn ($i) => [
                'url' => $i->url(),
                'alt' => $product->name,
            ])->values()->all(),
            'productUrl' => route('products.show', $product),
        ]);
    }
}
