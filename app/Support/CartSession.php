<?php

namespace App\Support;

use App\Models\Product;

class CartSession
{
    /**
     * Re-fetch products from the database, drop invalid lines, and cap quantities to stock.
     *
     * @return array<int, array{name: string, slug: string, price: string, list_price: string|null, quantity: int, image: string|null}>
     */
    public static function reconcile(): array
    {
        $cart = session()->get('cart', []);
        $cleaned = [];

        foreach ($cart as $productId => $line) {
            $productId = (int) $productId;
            $product = Product::query()->with('images')->find($productId);

            if (! $product || ! $product->is_active) {
                continue;
            }

            $qty = (int) ($line['quantity'] ?? 0);
            $qty = min(max(1, $qty), $product->stock);

            $cleaned[$productId] = self::lineItemFromProduct($product, $qty);
        }

        session()->put('cart', $cleaned);

        return $cleaned;
    }

    /**
     * @return array{name: string, slug: string, price: string, list_price: string|null, quantity: int, image: string|null}
     */
    public static function lineItemFromProduct(Product $product, int $quantity): array
    {
        $image = $product->images->first();
        $list = $product->listPrice();
        $effective = $product->effectivePrice();
        $listStr = (string) $list;
        $effStr = (string) $effective;

        return [
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => $effStr,
            'list_price' => $product->hasActiveDiscount() && abs($list - $effective) > 0.0001 ? $listStr : null,
            'quantity' => $quantity,
            'image' => $image?->image_path,
        ];
    }
}
