<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\StoreProductDisplaySetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layout', function ($view): void {
            $cart = session('cart', []);
            $cartCount = collect($cart)->sum(fn ($line) => (int) ($line['quantity'] ?? 0));
            $cartTotal = collect($cart)->sum(function ($line) {
                $qty = (int) ($line['quantity'] ?? 0);
                $price = (float) ($line['price'] ?? 0);

                return $qty * $price;
            });
            $view->with('cartCount', $cartCount);
            $view->with('cartTotal', $cartTotal);
            $view->with('productDisplay', StoreProductDisplaySetting::current());

            if (! request()->routeIs('dashboard.*')) {
                $view->with('categories', Category::query()->orderBy('name')->get());
            }
        });
    }
}
