<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SaleSpotlightCard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SaleSpotlightController extends Controller
{
    /**
     * Edit the 3 fixed slots shown in the homepage “On sale” section.
     */
    public function edit(): View
    {
        $positions = [0, 1, 2];

        $cardsByPosition = SaleSpotlightCard::query()
            ->whereIn('position', $positions)
            ->orderBy('position')
            ->get()
            ->keyBy('position');

        $cards = [];
        foreach ($positions as $pos) {
            $cards[$pos] = $cardsByPosition->get($pos);
        }

        $products = Product::query()
            ->active()
            ->where(function ($q) {
                $q->whereNotNull('discount_type')->where('discount_type', '!=', '')
                    ->orWhere('flash_sale', true);
            })
            ->orderByDesc('id')
            ->get();

        // If nothing is marked as “on sale” in the DB yet, fall back to all active products.
        if ($products->isEmpty()) {
            $products = Product::query()->active()->orderByDesc('id')->get();
        }

        return view('admin.sale-spotlight.edit', [
            'cards' => $cards,
            'products' => $products,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $positions = [0, 1, 2];

        $validated = $request->validate([
            'slots' => ['required', 'array'],
            'slots.*.product_id' => ['nullable', 'exists:products,id'],
            'slots.*.is_active' => ['nullable', 'boolean'],
            'slots.*.remove_image' => ['nullable', 'boolean'],
            'slots.*.image' => ['nullable', 'image', 'max:10240', 'mimes:jpeg,jpg,png,gif,webp,bmp'],
        ]);

        // Keep “Active” slots consistent: if a slot is enabled, it must point to a real product.
        foreach ($positions as $pos) {
            $isActive = $request->boolean("slots.$pos.is_active");
            $productId = $request->input("slots.$pos.product_id");
            if ($isActive && ($productId === null || $productId === '')) {
                throw ValidationException::withMessages([
                    "slots.$pos.product_id" => ['Product is required when this card is active.'],
                ]);
            }
        }

        $slots = $validated['slots'] ?? [];

        DB::transaction(function () use ($request, $slots, $positions) {
            foreach ($positions as $pos) {
                $slot = is_array($slots[$pos] ?? null) ? $slots[$pos] : [];

                $rawIsActive = $slot['is_active'] ?? false;
                $isActive = $rawIsActive === true || $rawIsActive === 1 || (string) $rawIsActive === '1';

                $productId = $slot['product_id'] ?? null;
                if ($productId === '' || $productId === 0) {
                    $productId = null;
                }

                if (! $isActive) {
                    $productId = null;
                }

                $card = SaleSpotlightCard::query()->where('position', $pos)->first();
                if ($card === null) {
                    $card = new SaleSpotlightCard();
                    $card->position = $pos;
                }

                // Image handling:
                $shouldRemoveImage = (bool) ($slot['remove_image'] ?? false);
                if ($shouldRemoveImage && $card->image_path) {
                    Storage::disk('public')->delete($card->image_path);
                    $card->image_path = null;
                }

                if ($request->hasFile("slots.$pos.image")) {
                    if ($card->image_path) {
                        Storage::disk('public')->delete($card->image_path);
                    }

                    $card->image_path = $request->file("slots.$pos.image")->store('sale-spotlight-cards', 'public');
                }

                $card->is_active = $isActive;
                $card->product_id = $productId;

                // Keep the row even if inactive so the admin edit form stays stable.
                $card->save();
            }
        });

        return redirect()
            ->route('dashboard.sale-spotlight.edit')
            ->with('status', 'On sale spotlight updated.');
    }
}

