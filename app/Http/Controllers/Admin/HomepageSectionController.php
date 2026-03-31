<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class HomepageSectionController extends Controller
{
    public function index(): View
    {
        $sections = HomepageSection::query()
            ->ordered()
            ->get()
            ->reject(fn (HomepageSection $s) => $s->type === HomepageSection::TYPE_CATEGORY_BLOCK)
            ->values();

        return view('admin.homepage-sections.index', compact('sections'));
    }

    public function create(): View
    {
        return view('admin.homepage-sections.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        $imagePath = $this->resolveImagePath($request, null);

        HomepageSection::query()->create([
            'type' => $validated['type'],
            'title' => $validated['title'] ?? null,
            'subtitle' => $validated['subtitle'] ?? null,
            'image' => $imagePath,
            'link' => $validated['link'] ?? null,
            'config' => $validated['config'],
            'position' => $validated['position'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('dashboard.homepage-sections.index')
            ->with('status', 'Homepage section created.');
    }

    public function edit(HomepageSection $homepage_section): View
    {
        return view('admin.homepage-sections.edit', ['section' => $homepage_section]);
    }

    public function update(Request $request, HomepageSection $homepage_section): RedirectResponse
    {
        $validated = $this->validated($request);

        $imagePath = $this->resolveImagePath($request, $homepage_section->image);

        $homepage_section->update([
            'type' => $validated['type'],
            'title' => $validated['title'] ?? null,
            'subtitle' => $validated['subtitle'] ?? null,
            'image' => $imagePath,
            'link' => $validated['link'] ?? null,
            'config' => $validated['config'],
            'position' => $validated['position'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('dashboard.homepage-sections.index')
            ->with('status', 'Homepage section updated.');
    }

    public function destroy(HomepageSection $homepage_section): RedirectResponse
    {
        $path = $homepage_section->image ?? '';
        if ($path !== '' && str_starts_with($path, 'homepage-sections/')) {
            Storage::disk('public')->delete($path);
        }

        // Clean up uploaded tile images for category blocks.
        if ($homepage_section->type === HomepageSection::TYPE_CATEGORY_BLOCK && is_array($homepage_section->config)) {
            $items = $homepage_section->config['items'] ?? [];
            if (is_array($items)) {
                foreach ($items as $row) {
                    $p = (string) ($row['image'] ?? '');
                    if ($p !== '' && str_starts_with($p, 'homepage-sections/tiles/')) {
                        Storage::disk('public')->delete($p);
                    }
                }
            }
        }
        $homepage_section->delete();

        return redirect()->route('dashboard.homepage-sections.index')
            ->with('status', 'Homepage section deleted.');
    }

    /**
     * @return array{type: string, title: ?string, subtitle: ?string, link: ?string, config: ?array, position: int}
     */
    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in(HomepageSection::TYPES)],
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:512'],
            'link' => ['nullable', 'string', 'max:2048'],
            'position' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['sometimes', 'boolean'],
            'image_path' => ['nullable', 'string', 'max:512'],
            'image' => ['nullable', 'image', 'max:10240'],
            'config_json' => ['nullable', 'string', 'max:65535'],
        ]);

        $config = null;
        $type = (string) ($validated['type'] ?? '');

        if ($type === HomepageSection::TYPE_CATEGORY_BLOCK) {
            // Cards, images, and copy are managed in Dashboard → Shop by category (category_banners).
            $config = ['items' => []];
        } else {
            if (isset($validated['config_json']) && trim((string) $validated['config_json']) !== '') {
                $decoded = json_decode(trim((string) $validated['config_json']), true);
                if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
                    throw ValidationException::withMessages([
                        'config_json' => 'Config must be valid JSON (object or array).',
                    ]);
                }
                $config = $decoded;
            }

            unset($validated['config_json']);
        }

        return array_merge($validated, ['config' => $config]);
    }

    private function resolveImagePath(Request $request, ?string $existing): string
    {
        if ($request->hasFile('image')) {
            if ($existing !== null && $existing !== '' && str_starts_with($existing, 'homepage-sections/')) {
                Storage::disk('public')->delete($existing);
            }

            return $request->file('image')->store('homepage-sections', 'public');
        }

        $path = trim((string) $request->input('image_path', ''));
        if ($path !== '') {
            return $path;
        }

        return (string) ($existing ?? '');
    }
}
