<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->with(['category', 'images'])
            ->latest()
            ->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'discount_type' => ['nullable', 'string', 'in:percent,fixed'],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'flash_sale' => ['sometimes', 'boolean'],
            'sale_end_time' => ['nullable', 'date'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_trending' => ['sometimes', 'boolean'],
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['required', 'file', 'max:10240', 'mimes:jpeg,jpg,png,gif,webp,bmp'],
            'image_color_labels_csv' => ['nullable', 'string', 'max:2000'],
        ]);

        $discountType = $validated['discount_type'] ?? null;
        $discountValue = $validated['discount_value'] ?? null;
        if ($discountType === null || $discountType === '') {
            $discountType = null;
            $discountValue = null;
        }

        DB::transaction(function () use ($validated, $request, $discountType, $discountValue) {
            $labels = $this->parseColorLabelsCsv($request->input('image_color_labels_csv'));

            $product = Product::query()->create([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'flash_sale' => $request->boolean('flash_sale'),
                'sale_end_time' => $validated['sale_end_time'] ?? null,
                'is_featured' => $request->boolean('is_featured'),
                'is_trending' => $request->boolean('is_trending'),
                'stock' => $validated['stock'],
                'is_active' => $request->boolean('is_active'),
            ]);

            foreach ($request->file('images') as $i => $file) {
                $path = $file->store('products', 'public');
                $product->images()->create([
                    'image_path' => $path,
                    'sort_order' => $i,
                    'color_label' => $labels[$i] ?? null,
                ]);
            }
        });

        return redirect()->route('dashboard.products.index')
            ->with('status', 'Product created.');
    }

    public function edit(Product $product): View
    {
        $product->load(['images']);
        $categories = Category::query()->orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'discount_type' => ['nullable', 'string', 'in:percent,fixed'],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'flash_sale' => ['sometimes', 'boolean'],
            'sale_end_time' => ['nullable', 'date'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_trending' => ['sometimes', 'boolean'],
            'remove_image_ids' => ['nullable', 'array'],
            'remove_image_ids.*' => ['integer'],
            'image_order' => ['nullable', 'array'],
            'image_order.*' => ['integer', Rule::exists('product_images', 'id')->where('product_id', $product->id)],
            'images' => ['nullable', 'array'],
            'images.*' => ['file', 'max:10240', 'mimes:jpeg,jpg,png,gif,webp,bmp'],
            'image_color_label' => ['nullable', 'array'],
            'image_color_label.*' => ['nullable', 'string', 'max:64'],
            'new_image_color_labels_csv' => ['nullable', 'string', 'max:2000'],
        ]);

        $removeIds = array_values(array_unique(array_map('intval', $validated['remove_image_ids'] ?? [])));
        $newFiles = $request->file('images') ?? [];
        $orderIds = collect($request->input('image_order', []))
            ->map(fn ($v) => (int) $v)
            ->filter(fn ($id) => $id > 0)
            ->values();

        $allImageIds = $product->images()->pluck('id');
        $keptIds = $allImageIds->diff($removeIds)->values();

        $ownedRemoveCount = $product->images()->whereIn('id', $removeIds)->count();
        if ($ownedRemoveCount !== count($removeIds)) {
            throw ValidationException::withMessages([
                'remove_image_ids' => ['Invalid image selection.'],
            ]);
        }

        $remaining = $keptIds->count() + count($newFiles);
        if ($remaining < 1) {
            throw ValidationException::withMessages([
                'images' => ['The product must have at least one image.'],
            ]);
        }

        if ($orderIds->isNotEmpty()) {
            $sortedOrder = $orderIds->sort()->values();
            $sortedKept = $keptIds->sort()->values();
            if ($sortedOrder->toArray() !== $sortedKept->toArray()) {
                throw ValidationException::withMessages([
                    'image_order' => ['Image order must list each remaining image exactly once.'],
                ]);
            }
        }

        $discountType = $validated['discount_type'] ?? null;
        $discountValue = $validated['discount_value'] ?? null;
        if ($discountType === null || $discountType === '') {
            $discountType = null;
            $discountValue = null;
        }

        DB::transaction(function () use ($validated, $request, $product, $removeIds, $newFiles, $orderIds, $discountType, $discountValue) {
            if ($removeIds !== []) {
                ProductImage::query()
                    ->where('product_id', $product->id)
                    ->whereIn('id', $removeIds)
                    ->delete();
            }

            $product->update([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'flash_sale' => $request->boolean('flash_sale'),
                'sale_end_time' => $validated['sale_end_time'] ?? null,
                'is_featured' => $request->boolean('is_featured'),
                'is_trending' => $request->boolean('is_trending'),
                'stock' => $validated['stock'],
                'is_active' => $request->boolean('is_active'),
            ]);

            $finalOrder = $orderIds->isNotEmpty()
                ? $orderIds
                : $product->images()->orderBy('sort_order')->orderBy('id')->pluck('id');

            foreach ($finalOrder->values() as $position => $imageId) {
                ProductImage::query()
                    ->where('product_id', $product->id)
                    ->where('id', $imageId)
                    ->update(['sort_order' => $position]);
            }

            $newLabels = $this->parseColorLabelsCsv($request->input('new_image_color_labels_csv'));

            $start = (int) $product->images()->count();
            foreach ($newFiles as $i => $file) {
                $path = $file->store('products', 'public');
                $product->images()->create([
                    'image_path' => $path,
                    'sort_order' => $start + $i,
                    'color_label' => $newLabels[$i] ?? null,
                ]);
            }

            $submitted = $request->input('image_color_label', []);
            foreach ($submitted as $imageId => $label) {
                $imageId = (int) $imageId;
                if ($imageId < 1) {
                    continue;
                }
                $normalized = is_string($label) ? trim($label) : '';
                ProductImage::query()
                    ->where('product_id', $product->id)
                    ->where('id', $imageId)
                    ->update([
                        'color_label' => $normalized === '' ? null : Str::limit($normalized, 64, ''),
                    ]);
            }
        });

        return redirect()->route('dashboard.products.edit', $product->fresh())
            ->with('status', 'Product saved.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('dashboard.products.index')
            ->with('status', 'Product deleted.');
    }

    /**
     * Comma-separated labels in the same order as parallel image uploads.
     *
     * @return list<string|null>
     */
    private function parseColorLabelsCsv(?string $csv): array
    {
        if ($csv === null || trim($csv) === '') {
            return [];
        }

        $parts = array_map('trim', explode(',', $csv));

        return array_map(function (string $s) {
            if ($s === '') {
                return null;
            }

            $t = Str::limit($s, 64, '');

            return $t === '' ? null : $t;
        }, $parts);
    }
}
