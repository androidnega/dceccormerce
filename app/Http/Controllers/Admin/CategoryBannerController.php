<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoryBanner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryBannerController extends Controller
{
    public function index(): View
    {
        $banners = CategoryBanner::query()->orderBy('position')->orderBy('id')->get();

        return view('admin/category-banners/index', compact('banners'));
    }

    public function create(): View
    {
        $types = CategoryBanner::TYPES;

        return view('admin/category-banners/create', compact('types'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in(CategoryBanner::TYPES)],
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:512'],
            'background_color' => ['nullable', 'string', 'max:32'],
            'text_color' => ['nullable', 'string', 'max:32'],
            'cta_text' => ['nullable', 'string', 'max:64'],
            'link' => ['nullable', 'string', 'max:2048'],
            'position' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'active' => ['sometimes', 'boolean'],
            'image' => ['nullable', 'file', 'max:15360', 'mimes:jpeg,jpg,png,gif,webp,bmp,avif,svg'],
            'video' => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime,video/webm', 'max:51200'],
            'video_url' => ['nullable', 'string', 'max:2048'],
            'image_width_percent' => ['nullable', 'integer', 'min:50', 'max:100'],
            'image_offset_y' => ['nullable', 'integer', 'min:-200', 'max:80'],
            'image_path_input' => ['nullable', 'string', 'max:2048'],
        ]);

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('category-banners/images', 'public')
            : self::normalizeImagePathInput($request->input('image_path_input'));
        $videoPath = $request->hasFile('video') ? $request->file('video')->store('category-banners/videos', 'public') : null;

        CategoryBanner::query()->create([
            'type' => $validated['type'],
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'image_path' => $imagePath,
            'image_width_percent' => (int) ($validated['image_width_percent'] ?? 90),
            'image_offset_y' => (int) ($validated['image_offset_y'] ?? -40),
            'video_path' => $videoPath,
            'video_url' => $validated['video_url'] ?? null,
            'background_color' => $validated['background_color'] ?? null,
            'text_color' => $validated['text_color'] ?? null,
            'cta_text' => $validated['cta_text'] ?? 'Shop Now',
            'link' => $validated['link'] ?? null,
            'position' => (int) ($validated['position'] ?? 0),
            'active' => $request->boolean('active', true),
        ]);

        return redirect()->route('dashboard.category-banners.index')->with('status', 'Category banner created.');
    }

    public function edit(CategoryBanner $category_banner): View
    {
        $types = CategoryBanner::TYPES;

        return view('admin/category-banners/edit', [
            'banner' => $category_banner,
            'types' => $types,
        ]);
    }

    public function update(Request $request, CategoryBanner $category_banner): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in(CategoryBanner::TYPES)],
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:512'],
            'background_color' => ['nullable', 'string', 'max:32'],
            'text_color' => ['nullable', 'string', 'max:32'],
            'cta_text' => ['nullable', 'string', 'max:64'],
            'link' => ['nullable', 'string', 'max:2048'],
            'position' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'active' => ['sometimes', 'boolean'],
            'image' => ['nullable', 'file', 'max:15360', 'mimes:jpeg,jpg,png,gif,webp,bmp,avif,svg'],
            'video' => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime,video/webm', 'max:51200'],
            'video_url' => ['nullable', 'string', 'max:2048'],
            'image_width_percent' => ['nullable', 'integer', 'min:50', 'max:100'],
            'image_offset_y' => ['nullable', 'integer', 'min:-200', 'max:80'],
            'image_path_input' => ['nullable', 'string', 'max:2048'],
        ]);

        $update = [
            'type' => $validated['type'],
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'background_color' => $validated['background_color'] ?? null,
            'text_color' => $validated['text_color'] ?? null,
            'cta_text' => $validated['cta_text'] ?? 'Shop Now',
            'link' => $validated['link'] ?? null,
            'position' => (int) ($validated['position'] ?? $category_banner->position),
            'active' => $request->boolean('active', true),
            'image_width_percent' => (int) ($validated['image_width_percent'] ?? $category_banner->image_width_percent ?? 90),
            'image_offset_y' => (int) ($validated['image_offset_y'] ?? $category_banner->image_offset_y ?? -40),
        ];

        if ($request->hasFile('image')) {
            self::deleteStoredImageIfApplicable($category_banner->image_path);
            $update['image_path'] = $request->file('image')->store('category-banners/images', 'public');
        } elseif (trim((string) $request->input('image_path_input')) !== '') {
            $newPath = self::normalizeImagePathInput($request->input('image_path_input'));
            if ($newPath !== null && $newPath !== $category_banner->image_path) {
                self::deleteStoredImageIfApplicable($category_banner->image_path);
                $update['image_path'] = $newPath;
            }
        }

        if ($request->hasFile('video')) {
            if ($category_banner->video_path) {
                Storage::disk('public')->delete($category_banner->video_path);
            }
            $update['video_path'] = $request->file('video')->store('category-banners/videos', 'public');
        }

        $category_banner->update($update);

        return redirect()->route('dashboard.category-banners.index')->with('status', 'Category banner updated.');
    }

    public function destroy(CategoryBanner $category_banner): RedirectResponse
    {
        self::deleteStoredImageIfApplicable($category_banner->image_path);
        if ($category_banner->video_path) {
            Storage::disk('public')->delete($category_banner->video_path);
        }
        $category_banner->delete();

        return redirect()->route('dashboard.category-banners.index')->with('status', 'Category banner deleted.');
    }

    private static function normalizeImagePathInput(?string $raw): ?string
    {
        $raw = trim((string) $raw);
        if ($raw === '') {
            return null;
        }
        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
            return $raw;
        }
        if (str_starts_with($raw, 'images/') || str_starts_with($raw, '/images/')) {
            return ltrim($raw, '/');
        }

        return $raw;
    }

    private static function deleteStoredImageIfApplicable(?string $path): void
    {
        $path = trim((string) $path);
        if ($path === '') {
            return;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return;
        }
        if (str_starts_with($path, 'images/') || str_starts_with($path, '/images/')) {
            return;
        }
        Storage::disk('public')->delete($path);
    }
}

