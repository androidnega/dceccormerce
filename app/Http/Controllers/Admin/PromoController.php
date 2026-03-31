<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PromoController extends Controller
{
    public function index(): View
    {
        $promos = Promo::query()->orderBy('sort_order')->orderBy('id')->paginate(20);

        return view('admin.promos.index', compact('promos'));
    }

    public function create(): View
    {
        return view('admin.promos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', Rule::in(Promo::TYPES)],
            'value' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'homepage_slot' => ['required', 'string', Rule::in(Promo::HOMEPAGE_SLOTS)],
            'is_active' => ['sometimes', 'boolean'],
            'media_kind' => ['required', 'string', Rule::in(Promo::MEDIA_KINDS)],
            'media_external_url' => ['nullable', 'string', 'max:512'],
            'promo_image' => ['nullable', 'file', 'image', 'max:10240'],
        ]);

        $mediaKind = $validated['media_kind'];
        $uploadPath = null;
        $external = isset($validated['media_external_url']) ? trim((string) $validated['media_external_url']) : '';

        if ($mediaKind === Promo::MEDIA_IMAGE && $request->hasFile('promo_image')) {
            $uploadPath = $request->file('promo_image')->store('promos', 'public');
        }

        if ($mediaKind === Promo::MEDIA_NONE) {
            $uploadPath = null;
            $external = '';
        }

        if ($mediaKind === Promo::MEDIA_VIDEO) {
            $uploadPath = null;
        }

        Promo::query()->create([
            'title' => $validated['title'],
            'type' => $validated['type'],
            'value' => $validated['value'] ?? '',
            'media_kind' => $mediaKind,
            'media_upload_path' => $uploadPath,
            'media_external_url' => $external !== '' ? $external : null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'homepage_slot' => $validated['homepage_slot'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('dashboard.promos.index')
            ->with('status', 'Promo created.');
    }

    public function edit(Promo $promo): View
    {
        return view('admin.promos.edit', compact('promo'));
    }

    public function update(Request $request, Promo $promo): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', Rule::in(Promo::TYPES)],
            'value' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'homepage_slot' => ['required', 'string', Rule::in(Promo::HOMEPAGE_SLOTS)],
            'is_active' => ['sometimes', 'boolean'],
            'media_kind' => ['required', 'string', Rule::in(Promo::MEDIA_KINDS)],
            'media_external_url' => ['nullable', 'string', 'max:512'],
            'promo_image' => ['nullable', 'file', 'image', 'max:10240'],
        ]);

        $mediaKind = $validated['media_kind'];
        $external = isset($validated['media_external_url']) ? trim((string) $validated['media_external_url']) : '';
        $uploadPath = $promo->media_upload_path;

        if ($request->boolean('remove_promo_image') && $uploadPath) {
            Storage::disk('public')->delete($uploadPath);
            $uploadPath = null;
        }

        if ($mediaKind === Promo::MEDIA_IMAGE && $request->hasFile('promo_image')) {
            if ($uploadPath) {
                Storage::disk('public')->delete($uploadPath);
            }
            $uploadPath = $request->file('promo_image')->store('promos', 'public');
        }

        if ($mediaKind === Promo::MEDIA_NONE) {
            if ($uploadPath) {
                Storage::disk('public')->delete($uploadPath);
            }
            $uploadPath = null;
            $external = '';
        }

        if ($mediaKind === Promo::MEDIA_VIDEO) {
            if ($uploadPath) {
                Storage::disk('public')->delete($uploadPath);
            }
            $uploadPath = null;
        }

        $promo->update([
            'title' => $validated['title'],
            'type' => $validated['type'],
            'value' => $validated['value'] ?? '',
            'media_kind' => $mediaKind,
            'media_upload_path' => $uploadPath,
            'media_external_url' => $external !== '' ? $external : null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'homepage_slot' => $validated['homepage_slot'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('dashboard.promos.edit', $promo)
            ->with('status', 'Promo saved.');
    }

    public function destroy(Promo $promo): RedirectResponse
    {
        if ($promo->media_upload_path) {
            Storage::disk('public')->delete($promo->media_upload_path);
        }
        $promo->delete();

        return redirect()->route('dashboard.promos.index')
            ->with('status', 'Promo deleted.');
    }
}
