<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HomepageSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HomepageSettingsController extends Controller
{
    public function edit(): View
    {
        $settings = HomepageSetting::current();
        $categories = Category::query()->orderBy('name')->get();
        $sidebarPadded = $settings->sidebarCategoryIdsPadded();
        $promoSlots = $settings->mergedPromoBanners();

        return view('admin.homepage-settings.edit', compact('settings', 'categories', 'sidebarPadded', 'promoSlots'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'homepage_layout' => ['required', 'in:'.HomepageSetting::LAYOUT_CAROUSEL.','.HomepageSetting::LAYOUT_SIDEBAR.','.HomepageSetting::LAYOUT_STACKED_CARDS],
            'stacked_cards_stage_bg_hex' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'hero_fullwidth_bg_hex' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'hero_fullwidth_text_hex' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'sidebar_category_ids' => ['nullable', 'array'],
            'sidebar_category_ids.*' => ['nullable', 'integer', 'exists:categories,id'],
            'promo_banners' => ['required', 'array', 'size:3'],
            'promo_banners.*.title_line1' => ['required', 'string', 'max:255'],
            'promo_banners.*.title_line2' => ['required', 'string', 'max:255'],
            'promo_banners.*.price_label' => ['required', 'string', 'max:64'],
            'promo_banners.*.background_hex' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'promo_banners.*.link_url' => ['nullable', 'string', 'max:2048'],
            'promo_banners.*.image' => ['nullable', 'image', 'max:10240'],
        ]);

        $settings = HomepageSetting::current();
        $raw = $request->input('sidebar_category_ids', []);
        $normalized = [];
        for ($i = 0; $i < 9; $i++) {
            $v = $raw[$i] ?? null;
            $normalized[] = $v !== null && $v !== '' ? (int) $v : null;
        }
        $chosen = array_values(array_filter($normalized, fn ($id) => $id !== null));
        if (count($chosen) !== count(array_unique($chosen))) {
            return redirect()->route('dashboard.homepage-settings.edit')
                ->withErrors(['sidebar_category_ids' => 'Each category can only be selected in one row. Remove duplicates and try again.'])
                ->withInput();
        }

        $defaults = HomepageSetting::defaultPromoBanners();
        $mergedPrev = $settings->mergedPromoBanners();
        $promoOut = [];
        foreach (range(0, 2) as $i) {
            $row = [
                'title_line1' => $validated['promo_banners'][$i]['title_line1'],
                'title_line2' => $validated['promo_banners'][$i]['title_line2'],
                'price_label' => HomepageSetting::normalizePromoPriceLabel($validated['promo_banners'][$i]['price_label']),
                'background_hex' => $validated['promo_banners'][$i]['background_hex'],
                'link_url' => $validated['promo_banners'][$i]['link_url'] ?? '/',
            ];
            $path = $mergedPrev[$i]['image_path'] ?? $defaults[$i]['image_path'];
            if ($request->hasFile("promo_banners.$i.image")) {
                if ($path !== '' && str_starts_with($path, 'promo-banners/')) {
                    Storage::disk('public')->delete($path);
                }
                $path = $request->file("promo_banners.$i.image")->store('promo-banners', 'public');
            }
            $row['image_path'] = $path;
            $promoOut[] = $row;
        }

        $settings->update([
            'homepage_layout' => $validated['homepage_layout'],
            'stacked_cards_stage_bg_hex' => $validated['stacked_cards_stage_bg_hex'],
            'hero_fullwidth_bg_hex' => $validated['hero_fullwidth_bg_hex'],
            'hero_fullwidth_text_hex' => $validated['hero_fullwidth_text_hex'],
            'sidebar_category_ids' => $normalized,
            'promo_banners' => $promoOut,
        ]);

        return redirect()->route('dashboard.homepage-settings.edit')
            ->with('status', 'Homepage settings saved.');
    }
}
