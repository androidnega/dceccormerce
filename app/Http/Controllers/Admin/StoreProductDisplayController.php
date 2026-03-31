<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreProductDisplaySetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreProductDisplayController extends Controller
{
    public function edit(): View
    {
        $settings = StoreProductDisplaySetting::current();

        return view('admin.store-product-display.edit', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_layout' => ['required', 'in:'.implode(',', StoreProductDisplaySetting::layoutOptions())],
            'enable_hover_actions' => ['sometimes', 'boolean'],
            'enable_quick_view' => ['sometimes', 'boolean'],
            'enable_wishlist' => ['sometimes', 'boolean'],
            'enable_image_hover_swap' => ['sometimes', 'boolean'],
            'card_size' => ['required', 'in:'.implode(',', StoreProductDisplaySetting::cardSizeOptions())],
            'featured_products_display' => ['required', 'in:'.implode(',', StoreProductDisplaySetting::featuredProductsDisplayOptions())],
        ]);

        $settings = StoreProductDisplaySetting::current();
        $settings->update([
            'product_layout' => $validated['product_layout'],
            'enable_hover_actions' => $request->boolean('enable_hover_actions'),
            'enable_quick_view' => $request->boolean('enable_quick_view'),
            'enable_wishlist' => $request->boolean('enable_wishlist'),
            'enable_image_hover_swap' => $request->boolean('enable_image_hover_swap'),
            'card_size' => $validated['card_size'],
            'featured_products_display' => $validated['featured_products_display'],
        ]);

        return redirect()->route('dashboard.store-product-display.edit')
            ->with('status', 'Product display settings saved.');
    }
}
