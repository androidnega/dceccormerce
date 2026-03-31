@php
    $section = $section ?? null;
    $configJson = old('config_json');
    if ($configJson === null && $section !== null && $section->config !== null) {
        $configJson = json_encode($section->config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    $initialType = old('type', $section?->type ?? '');
    $showImageFields = in_array($initialType, ['featured_promo', 'slider'], true);
    $showConfigFields = in_array($initialType, ['product_grid', 'flash_section', 'slider'], true);

@endphp
<div>
    <label class="block text-sm font-medium text-neutral-800">Section type</label>
    <select name="type" required class="mt-1 w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-[#0057b8] focus:outline-none focus:ring-2 focus:ring-[#0057b8]/25">
        @foreach (\App\Models\HomepageSection::typesForAdmin($section?->type) as $t)
            <option value="{{ $t }}" @selected(old('type', $section?->type) === $t)>{{ $t }}</option>
        @endforeach
    </select>
    @error('type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-sm font-medium text-neutral-800">Title</label>
    <input type="text" name="title" value="{{ old('title', $section?->title) }}" maxlength="255"
        class="mt-1 w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-[#0057b8] focus:outline-none focus:ring-2 focus:ring-[#0057b8]/25">
    @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-sm font-medium text-neutral-800">Subtitle</label>
    <input type="text" name="subtitle" value="{{ old('subtitle', $section?->subtitle) }}" maxlength="512"
        class="mt-1 w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-[#0057b8] focus:outline-none focus:ring-2 focus:ring-[#0057b8]/25">
    @error('subtitle')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-sm font-medium text-neutral-800">Link URL</label>
    <input type="text" name="link" value="{{ old('link', $section?->link) }}"
        class="mt-1 w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-[#0057b8] focus:outline-none focus:ring-2 focus:ring-[#0057b8]/25"
        placeholder="/shop or https://…">
    @error('link')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="block text-sm font-medium text-neutral-800">Position (sort order)</label>
        <input type="number" name="position" value="{{ old('position', $section?->position ?? 0) }}" min="0" max="9999"
            class="mt-1 w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm text-neutral-900 shadow-sm">
        @error('position')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="flex items-end pb-1">
        <label class="flex cursor-pointer items-center gap-2 text-sm text-neutral-800">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="rounded border-neutral-300 text-[#0057b8]" @checked(old('is_active', $section?->is_active ?? true))>
            Active on storefront
        </label>
    </div>
</div>
<div data-show-types="featured_promo,slider" class="{{ $showImageFields ? '' : 'hidden' }}">
    <div>
        <label class="block text-sm font-medium text-neutral-800">Image — upload</label>
        <input type="file" name="image" accept="image/*" class="mt-1 block w-full text-sm text-neutral-600 file:mr-3 file:rounded-lg file:border-0 file:bg-[#0057b8] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-[#00479a]">
        @error('image')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <p class="mt-2 text-xs text-neutral-500">
        For <code class="rounded bg-neutral-100 px-1 py-0.5 font-mono text-[11px]">featured_promo</code> (like “Elegant collection”), this image is what the storefront shows.
        Config JSON is not required for this type.
    </p>
    <div>
        <label class="block text-sm font-medium text-neutral-800">Or image path (under public)</label>
        <input type="text" name="image_path" value="{{ old('image_path', $section && $section->image && !str_starts_with($section->image, 'homepage-sections/') ? $section->image : '') }}"
            class="mt-1 w-full rounded-lg border border-neutral-200 px-3 py-2 font-mono text-sm text-neutral-900 shadow-sm"
            placeholder="images/your-banner.webp">
        <p class="mt-1 text-xs text-neutral-500">Leave empty to keep the current uploaded file when editing.</p>
        @error('image_path')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    @if ($section !== null && $section->image !== null && $section->image !== '' && str_starts_with($section->image, 'homepage-sections/'))
        <p class="text-xs text-neutral-600">Current file: <code class="rounded bg-neutral-100 px-1">{{ $section->image }}</code></p>
    @endif
</div>

<div data-show-types="category_block" class="@if($initialType === 'category_block') '' @else 'hidden' @endif space-y-4">
    <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-5 text-sm text-zinc-800">
        <div class="text-base font-semibold text-zinc-900">Shop by category (homepage cards)</div>
        <p class="mt-2 text-sm leading-relaxed text-zinc-600">
            The three large cards (images, titles, links, and the wide video card) are edited in one place:
            <a href="{{ route('dashboard.category-banners.index') }}" class="font-medium text-zinc-900 underline decoration-zinc-300 underline-offset-2 hover:decoration-zinc-900">Shop by category</a>
            in the sidebar. This homepage section only turns the block on or off and sets its sort order above.
        </p>
    </div>
</div>

<div data-show-types="product_grid,flash_section,slider" class="{{ $showConfigFields ? '' : 'hidden' }}">
    <div>
        <label class="block text-sm font-medium text-neutral-800">Config (JSON, optional)</label>
        <textarea name="config_json" rows="10" class="mt-1 w-full rounded-lg border border-neutral-200 px-3 py-2 font-mono text-xs text-neutral-900 shadow-sm focus:border-[#0057b8] focus:outline-none focus:ring-2 focus:ring-[#0057b8]/25" placeholder='{"items":[...]} or {"source":"latest","limit":8}'>{{ $configJson }}</textarea>
        <p class="mt-1 text-xs text-neutral-500">
            <strong>product_grid / flash_section:</strong> <code class="rounded bg-neutral-100 px-0.5">{"source":"latest|featured|sale|trending","limit":8}</code>
            · <strong>slider banner:</strong> <code class="rounded bg-neutral-100 px-0.5">{"variant":"banner"}</code> (uses main image)
        </p>
        @error('config_json')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div data-show-types="soft_promo" class="@if($initialType === 'soft_promo') '' @else 'hidden' @endif">
    <div class="rounded-lg border border-neutral-200 bg-neutral-50 p-3 text-sm text-neutral-700">
        <div class="font-semibold text-neutral-900">No config needed</div>
        <div class="mt-1 text-xs text-neutral-600">
            This section only uses Title/Subtitle/Link.
        </div>
    </div>
</div>

<script>
    (function () {
        var select = document.querySelector('select[name="type"]');
        if (!select) return;

        function updateVisibility() {
            var t = select.value;
            document.querySelectorAll('[data-show-types]').forEach(function (el) {
                var showTypes = (el.getAttribute('data-show-types') || '').split(',').map(function (s) { return s.trim(); }).filter(Boolean);
                var show = showTypes.indexOf(t) !== -1;
                el.classList.toggle('hidden', !show);
            });
        }

        select.addEventListener('change', updateVisibility);
        updateVisibility();
    })();
</script>
