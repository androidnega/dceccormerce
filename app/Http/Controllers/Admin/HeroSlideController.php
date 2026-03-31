<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HeroSlideController extends Controller
{
    public function index(): View
    {
        $slides = HeroSlide::query()
            ->with('product')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.hero-slides.index', compact('slides'));
    }

    public function create(): View
    {
        $products = Product::query()->active()->orderBy('name')->get();

        return view('admin.hero-slides.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subheading' => ['required', 'string', 'max:255'],
            'headline' => ['required', 'string', 'max:255'],
            'headline_line2' => ['nullable', 'string', 'max:255'],
            'cta_label' => ['nullable', 'string', 'max:64'],
            'cta_url' => ['nullable', 'string', 'max:2048'],
            'product_id' => ['nullable', 'exists:products,id'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['sometimes', 'boolean'],
            'background_hex' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'image' => ['required', 'image', 'max:8192'],
        ]);

        $path = $request->file('image')->store('hero-slides', 'public');

        HeroSlide::query()->create([
            'subheading' => $validated['subheading'],
            'headline' => $validated['headline'],
            'headline_line2' => $validated['headline_line2'] ?? null,
            'cta_label' => $validated['cta_label'] ?? 'Shop now',
            'cta_url' => $validated['cta_url'] ?? null,
            'product_id' => $validated['product_id'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'image_path' => $path,
            'background_hex' => $validated['background_hex'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('dashboard.hero-slides.index')
            ->with('status', 'Slide created.');
    }

    public function edit(HeroSlide $heroSlide): View
    {
        $heroSlide->load('product');
        $products = Product::query()->active()->orderBy('name')->get();

        return view('admin.hero-slides.edit', compact('heroSlide', 'products'));
    }

    public function update(Request $request, HeroSlide $heroSlide): RedirectResponse
    {
        $validated = $request->validate([
            'subheading' => ['required', 'string', 'max:255'],
            'headline' => ['required', 'string', 'max:255'],
            'headline_line2' => ['nullable', 'string', 'max:255'],
            'cta_label' => ['nullable', 'string', 'max:64'],
            'cta_url' => ['nullable', 'string', 'max:2048'],
            'product_id' => ['nullable', 'exists:products,id'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['sometimes', 'boolean'],
            'background_hex' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'image' => ['nullable', 'image', 'max:8192'],
        ]);

        if ($request->hasFile('image')) {
            if ($heroSlide->image_path !== null) {
                Storage::disk('public')->delete($heroSlide->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('hero-slides', 'public');
        }

        $update = [
            'subheading' => $validated['subheading'],
            'headline' => $validated['headline'],
            'headline_line2' => $validated['headline_line2'] ?? null,
            'cta_label' => $validated['cta_label'] ?? 'Shop now',
            'cta_url' => $validated['cta_url'] ?? null,
            'product_id' => $validated['product_id'] ?? null,
            'sort_order' => $validated['sort_order'] ?? $heroSlide->sort_order,
            'background_hex' => $validated['background_hex'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ];
        if (isset($validated['image_path'])) {
            $update['image_path'] = $validated['image_path'];
        }
        $heroSlide->update($update);

        return redirect()->route('dashboard.hero-slides.index')
            ->with('status', 'Slide updated.');
    }

    public function destroy(HeroSlide $heroSlide): RedirectResponse
    {
        if ($heroSlide->image_path !== null) {
            Storage::disk('public')->delete($heroSlide->image_path);
        }
        $heroSlide->delete();

        return redirect()->route('dashboard.hero-slides.index')
            ->with('status', 'Slide deleted.');
    }
}
