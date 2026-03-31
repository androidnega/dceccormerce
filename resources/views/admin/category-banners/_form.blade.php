@php
    $banner = $banner ?? null;
    $type = old('type', $banner?->type ?? \App\Models\CategoryBanner::TYPE_IMAGE);
@endphp

<div class="space-y-8">
    <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4 text-sm text-zinc-700">
        <p class="font-medium text-zinc-900">What you’re editing</p>
        <p class="mt-1 text-sm leading-relaxed text-zinc-600">
            The storefront shows the <strong class="font-medium text-zinc-800">first three active cards</strong> (by position). Image cards use a large product photo; the wide card can be a video.
        </p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-zinc-800">Card type</label>
            <select name="type" data-category-banner-type class="mt-1 w-full rounded-lg border border-zinc-200 bg-white px-3 py-2.5 text-sm text-zinc-900 shadow-sm focus:border-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-200">
                <option value="{{ \App\Models\CategoryBanner::TYPE_IMAGE }}" @selected($type === \App\Models\CategoryBanner::TYPE_IMAGE)>Image — photo card (phones, Mac, etc.)</option>
                <option value="{{ \App\Models\CategoryBanner::TYPE_VIDEO }}" @selected($type === \App\Models\CategoryBanner::TYPE_VIDEO)>Video — full-width background video</option>
            </select>
            @error('type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-zinc-800">Title</label>
            <input type="text" name="title" value="{{ old('title', $banner?->title) }}" maxlength="255" required
                class="mt-1 w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-200">
            @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-zinc-800">Subtitle</label>
            <input type="text" name="subtitle" value="{{ old('subtitle', $banner?->subtitle) }}" maxlength="512"
                class="mt-1 w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-200">
            @error('subtitle')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-800">Button label</label>
            <input type="text" name="cta_text" value="{{ old('cta_text', $banner?->cta_text) }}" maxlength="64"
                class="mt-1 w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-200"
                placeholder="Shop Now">
        </div>
        <div>
            <label class="block text-sm font-medium text-zinc-800">Link</label>
            <input type="text" name="link" value="{{ old('link', $banner?->link) }}" maxlength="2048"
                class="mt-1 w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-200"
                placeholder="/shop/category/iphones">
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-800">Text color</label>
            <input type="text" name="text_color" value="{{ old('text_color', $banner?->text_color) }}" maxlength="32"
                class="mt-1 w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 font-mono text-sm text-zinc-900 shadow-sm focus:border-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-200"
                placeholder="#ffffff">
        </div>
        <div>
            <label class="block text-sm font-medium text-zinc-800">Background color</label>
            <p class="mt-0.5 text-xs text-zinc-500">Used for the video card, or image cards without an image.</p>
            <input type="text" name="background_color" value="{{ old('background_color', $banner?->background_color) }}" maxlength="32"
                class="mt-1 w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 font-mono text-sm text-zinc-900 shadow-sm focus:border-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-200"
                placeholder="#b06a70">
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-800">Sort position</label>
            <input type="number" name="position" value="{{ old('position', $banner?->position ?? 0) }}" min="0" max="9999"
                class="mt-1 w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm">
        </div>
        <div class="flex items-end pb-1">
            <label class="flex cursor-pointer items-center gap-2 text-sm text-zinc-800">
                <input type="hidden" name="active" value="0">
                <input type="checkbox" name="active" value="1" class="rounded border-zinc-300 text-zinc-900" @checked(old('active', $banner?->active ?? true))>
                Active on storefront
            </label>
        </div>
    </div>

    {{-- Image card --}}
    <div data-category-panel="{{ \App\Models\CategoryBanner::TYPE_IMAGE }}" class="@if($type !== \App\Models\CategoryBanner::TYPE_IMAGE) hidden @endif space-y-4 rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
        <div class="border-b border-zinc-100 pb-3">
            <h3 class="text-sm font-semibold text-zinc-900">Image for this card</h3>
            <p class="mt-1 text-xs text-zinc-500">Upload a PNG or WebP, or paste a URL / path. This is what shoppers see on the storefront.</p>
        </div>

        @if ($banner?->imageUrl())
            <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white">
                <img src="{{ $banner->imageUrl() }}" alt="" class="max-h-48 w-full object-contain object-center">
            </div>
        @endif

        <div>
            <label class="block text-sm font-medium text-zinc-800">Upload image</label>
            <p class="mt-0.5 text-xs text-zinc-500">JPEG, PNG, GIF, WebP, AVIF, BMP, or SVG (including <code class="rounded bg-zinc-100 px-1">.webp</code>).</p>
            <input type="file" name="image"
                accept="image/jpeg,image/png,image/gif,image/webp,image/bmp,image/avif,image/svg+xml,.jpg,.jpeg,.png,.gif,.webp,.bmp,.avif,.svg"
                class="mt-1 block w-full text-sm text-zinc-600 file:mr-3 file:rounded-lg file:border-0 file:bg-zinc-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-zinc-800">
            @error('image')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-800">Or paste image address</label>
            <p class="mt-0.5 text-xs text-zinc-500">Full <code class="rounded bg-zinc-100 px-1">https://…</code> link, or a path under <code class="rounded bg-zinc-100 px-1">public</code> like <code class="rounded bg-zinc-100 px-1">images/phones/…</code>. Leave empty when editing to keep the current image.</p>
            <input type="text" name="image_path_input" value="{{ old('image_path_input') }}" maxlength="2048"
                class="mt-1 w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 font-mono text-sm text-zinc-900 shadow-sm focus:border-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-200"
                placeholder="https://… or images/…">
            @error('image_path_input')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-zinc-800">Image width (%)</label>
                <p class="mt-0.5 text-xs text-zinc-500">100 = widest; 90 is the default for a slightly smaller photo inside the card.</p>
                <input type="number" name="image_width_percent" value="{{ old('image_width_percent', $banner?->image_width_percent ?? 90) }}" min="50" max="100"
                    class="mt-1 w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm">
                @error('image_width_percent')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-800">Vertical nudge (px)</label>
                <p class="mt-0.5 text-xs text-zinc-500">Negative moves the image up.</p>
                <input type="number" name="image_offset_y" value="{{ old('image_offset_y', $banner?->image_offset_y ?? -40) }}" min="-200" max="80"
                    class="mt-1 w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm">
                @error('image_offset_y')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- Video card --}}
    <div data-category-panel="{{ \App\Models\CategoryBanner::TYPE_VIDEO }}" class="@if($type !== \App\Models\CategoryBanner::TYPE_VIDEO) hidden @endif space-y-4 rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
        <div class="border-b border-zinc-100 pb-3">
            <h3 class="text-sm font-semibold text-zinc-900">Video for this card</h3>
            <p class="mt-1 text-xs text-zinc-500">Use a YouTube link, or upload a short MP4.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-800">YouTube URL</label>
            <input type="text" name="video_url" value="{{ old('video_url', $banner?->video_url) }}" maxlength="2048"
                class="mt-1 w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-200"
                placeholder="https://www.youtube.com/watch?v=…">
            @error('video_url')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-800">Upload video file</label>
            <input type="file" name="video" accept="video/*"
                class="mt-1 block w-full text-sm text-zinc-600 file:mr-3 file:rounded-lg file:border-0 file:bg-zinc-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-zinc-800">
            @error('video')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            @if ($banner?->video_path)<p class="mt-1 text-xs text-zinc-500">Current file: <code class="rounded bg-zinc-100 px-1">{{ $banner->video_path }}</code></p>@endif
        </div>
    </div>
</div>

<script>
    (function () {
        var select = document.querySelector('select[data-category-banner-type]');
        if (!select) return;
        function sync() {
            var t = select.value;
            document.querySelectorAll('[data-category-panel]').forEach(function (el) {
                var show = el.getAttribute('data-category-panel') === t;
                el.classList.toggle('hidden', !show);
            });
        }
        select.addEventListener('change', sync);
        sync();
    })();
</script>
