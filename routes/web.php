<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\HeroSlideController as AdminHeroSlideController;
use App\Http\Controllers\Admin\HomepageSettingsController as AdminHomepageSettingsController;
use App\Http\Controllers\Admin\StoreProductDisplayController as AdminStoreProductDisplayController;
use App\Http\Controllers\Admin\IntegrationsController as AdminIntegrationsController;
use App\Http\Controllers\Admin\HomepageSectionController as AdminHomepageSectionController;
use App\Http\Controllers\Admin\NewsPostController as AdminNewsPostController;
use App\Http\Controllers\Admin\PromoController as AdminPromoController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\RiderController as AdminRiderController;
use App\Http\Controllers\Admin\CategoryBannerController as AdminCategoryBannerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Rider\RiderController as RiderRiderController;
use App\Http\Controllers\Admin\DeliveryAgentController as AdminDeliveryAgentController;
use App\Http\Controllers\Admin\DeliveryRuleController as AdminDeliveryRuleController;
use App\Http\Controllers\Admin\SaleSpotlightController as AdminSaleSpotlightController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::redirect('/admin', '/dashboard', 301);
Route::get('/admin/{path}', function (string $path) {
    $path = trim($path, '/');
    if ($path === '' || $path === 'dashboard') {
        return redirect('/dashboard', 301);
    }

    return redirect('/dashboard/'.$path, 301);
})->where('path', '.*');

Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/shop', [ProductController::class, 'catalog'])->name('products.index');
Route::get('/shop/category/{category:slug}', [ProductController::class, 'category'])->name('shop.category');
Route::get('/shop/product/{product}', [ProductController::class, 'show'])->name('products.show');
Route::post('/shop/product/{product}/rate', [ProductController::class, 'rate'])->middleware('auth')->name('products.rate');
Route::get('/shop/product/{product}/quick-view', [ProductController::class, 'quickView'])->name('products.quick-view');
Route::get('/shop/product/{product}/image/{productImage}', [ProductController::class, 'imageOpen'])->name('products.image.open');
Route::get('/products', fn () => redirect()->route('products.index', request()->query(), 301));
Route::get('/product/{product}', fn ($product) => redirect()->route('products.show', ['product' => $product], 301));
Route::get('/product/{product}/quick-view', fn ($product) => redirect()->route('products.quick-view', ['product' => $product], 301));
Route::get('/product/{product}/image/{productImage}', fn ($product, $productImage) => redirect()->route('products.image.open', ['product' => $product, 'productImage' => $productImage], 301));

Route::post('/wishlist/toggle/{id}', [WishlistController::class, 'toggle'])->name('wishlist.toggle')->whereNumber('id');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/drawer', [CartController::class, 'drawer'])->name('cart.drawer');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add')->whereNumber('id');
Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update')->whereNumber('id');
Route::match(['get', 'post', 'delete'], '/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove')->whereNumber('id');
Route::get('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/delivery-options', [CheckoutController::class, 'deliveryOptions'])->name('checkout.delivery-options');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

Route::get('/track', [TrackingController::class, 'index'])->name('tracking.index');
Route::post('/track', [TrackingController::class, 'lookup'])->name('tracking.lookup');
Route::get('/track/{order_number}/status', [TrackingController::class, 'status'])->name('orders.track.status');
Route::get('/track/{order_number}', [TrackingController::class, 'show'])->name('orders.track');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::redirect('/rider', '/rider/dashboard', 301);

Route::middleware(['auth', 'rider'])->prefix('rider')->name('rider.')->group(function () {
    Route::get('/dashboard', [RiderRiderController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders/{order}', [RiderRiderController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{order}/on-the-way', [RiderRiderController::class, 'markOnTheWay'])->name('orders.on-the-way');
    Route::post('/orders/{order}/deliver', [RiderRiderController::class, 'markDelivered'])->name('orders.deliver');
    Route::post('/orders/{order}/fail', [RiderRiderController::class, 'markFailed'])->name('orders.fail');
});

Route::middleware(['auth', 'customer'])->prefix('account')->name('account.')->group(function () {
    Route::get('/', [CustomerController::class, 'dashboard'])->name('index');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
});

Route::middleware(['auth', 'staff'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('index');

    Route::middleware('manager')->group(function () {
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/confirm', [AdminOrderController::class, 'confirmOrder'])->name('orders.confirm');
        Route::post('/orders/{order}/assign-delivery-agent', [AdminOrderController::class, 'assignDeliveryAgent'])->name('orders.assign-delivery-agent');
        Route::post('/orders/{order}/prepare', [AdminOrderController::class, 'prepareOrder'])->name('orders.prepare');
        Route::post('/orders/{order}/on-the-way', [AdminOrderController::class, 'markOnTheWay'])->name('orders.on-the-way');
        Route::post('/orders/{order}/deliver', [AdminOrderController::class, 'markDelivered'])->name('orders.deliver');
        Route::post('/orders/{order}/delivered', [AdminOrderController::class, 'markDelivered'])->name('orders.delivered');
        Route::post('/orders/{order}/failed', [AdminOrderController::class, 'markFailed'])->name('orders.failed');
        Route::post('/orders/{order}/fail', [AdminOrderController::class, 'markFailed'])->name('orders.fail');
        Route::post('/orders/{order}/notes', [AdminOrderController::class, 'updateNotes'])->name('orders.notes');
        Route::get('/riders', [AdminRiderController::class, 'index'])->name('riders.index');
        Route::get('/riders/create', [AdminRiderController::class, 'create'])->name('riders.create');
        Route::post('/riders', [AdminRiderController::class, 'store'])->name('riders.store');
        Route::resource('delivery-agents', AdminDeliveryAgentController::class)->except(['show']);
    });

    Route::middleware('admin')->group(function () {
        Route::resource('delivery-rules', AdminDeliveryRuleController::class)->except(['show']);
        Route::resource('categories', AdminCategoryController::class)->except(['show']);
        Route::resource('products', AdminProductController::class)->except(['show']);
        Route::resource('hero-slides', AdminHeroSlideController::class)->except(['show']);
        Route::resource('news-posts', AdminNewsPostController::class)->except(['show']);
        Route::resource('homepage-sections', AdminHomepageSectionController::class)->except(['show']);
        Route::resource('category-banners', AdminCategoryBannerController::class)->except(['show']);
        Route::resource('promos', AdminPromoController::class)->except(['show']);
        Route::get('homepage-settings', [AdminHomepageSettingsController::class, 'edit'])->name('homepage-settings.edit');
        Route::put('homepage-settings', [AdminHomepageSettingsController::class, 'update'])->name('homepage-settings.update');
        Route::get('store-product-display', [AdminStoreProductDisplayController::class, 'edit'])->name('store-product-display.edit');
        Route::put('store-product-display', [AdminStoreProductDisplayController::class, 'update'])->name('store-product-display.update');
        Route::get('sale-spotlight', [AdminSaleSpotlightController::class, 'edit'])->name('sale-spotlight.edit');
        Route::put('sale-spotlight', [AdminSaleSpotlightController::class, 'update'])->name('sale-spotlight.update');
        Route::get('integrations', [AdminIntegrationsController::class, 'edit'])->name('integrations.edit');
        Route::put('integrations', [AdminIntegrationsController::class, 'update'])->name('integrations.update');
    });
});
