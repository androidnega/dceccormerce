@extends('layouts.dashboard')

@section('title', 'Edit product — Admin')
@section('heading', 'Edit product')
@section('subheading', 'Drag images to reorder. The first image is the main photo on the store.')

@section('content')
    <form id="product-edit-form" action="{{ route('dashboard.products.update', $product) }}" method="post" enctype="multipart/form-data" class="mt-0 max-w-2xl space-y-5">
        @csrf
        @method('PUT')
        <div>
            <label for="category_id" class="block text-sm font-medium text-slate-700">Category <span class="text-red-600">*</span></label>
            <select name="category_id" id="category_id" required
                class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id) == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700">Name <span class="text-red-600">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required
                class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
        </div>
        <div>
            <label for="description" class="block text-sm font-medium text-slate-700">Description</label>
            <textarea name="description" id="description" rows="4"
                class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">{{ old('description', $product->description) }}</textarea>
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="price" class="block text-sm font-medium text-slate-700">Price (GHS) <span class="text-red-600">*</span></label>
                <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" required
                    class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
            </div>
            <div>
                <label for="stock" class="block text-sm font-medium text-slate-700">Stock <span class="text-red-600">*</span></label>
                <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}" min="0" required
                    class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
            </div>
        </div>
        <div class="flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" id="is_active" value="1" class="rounded border-slate-300 text-slate-900 focus:ring-slate-500" @checked(old('is_active', $product->is_active))>
            <label for="is_active" class="text-sm text-slate-700">Active (visible on storefront)</label>
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="discount_type" class="block text-sm font-medium text-slate-700">Discount type</label>
                <select name="discount_type" id="discount_type" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                    <option value="">None</option>
                    <option value="percent" @selected(old('discount_type', $product->discount_type) === 'percent')>Percent off</option>
                    <option value="fixed" @selected(old('discount_type', $product->discount_type) === 'fixed')>Fixed amount off</option>
                </select>
            </div>
            <div>
                <label for="discount_value" class="block text-sm font-medium text-slate-700">Discount value</label>
                <input type="number" name="discount_value" id="discount_value" value="{{ old('discount_value', $product->discount_value) }}" step="0.01" min="0" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
            </div>
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div class="flex items-center gap-2">
                <input type="hidden" name="flash_sale" value="0">
                <input type="checkbox" name="flash_sale" id="flash_sale" value="1" class="rounded border-slate-300 text-slate-900 focus:ring-slate-500" @checked(old('flash_sale', $product->flash_sale))>
                <label for="flash_sale" class="text-sm text-slate-700">Flash sale</label>
            </div>
            <div>
                <label for="sale_end_time" class="block text-sm font-medium text-slate-700">Sale ends</label>
                <input type="datetime-local" name="sale_end_time" id="sale_end_time" value="{{ old('sale_end_time', $product->sale_end_time?->format('Y-m-d\TH:i')) }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
            </div>
        </div>
        <div class="flex flex-wrap gap-6">
            <div class="flex items-center gap-2">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" name="is_featured" id="is_featured" value="1" class="rounded border-slate-300 text-slate-900 focus:ring-slate-500" @checked(old('is_featured', $product->is_featured))>
                <label for="is_featured" class="text-sm text-slate-700">Featured</label>
            </div>
            <div class="flex items-center gap-2">
                <input type="hidden" name="is_trending" value="0">
                <input type="checkbox" name="is_trending" id="is_trending" value="1" class="rounded border-slate-300 text-slate-900 focus:ring-slate-500" @checked(old('is_trending', $product->is_trending))>
                <label for="is_trending" class="text-sm text-slate-700">Trending badge</label>
            </div>
        </div>

        @if ($product->images->isNotEmpty())
            <div>
                <p class="text-sm font-medium text-slate-700">Current images</p>
                <p class="text-xs text-slate-500">Drag by the handle to reorder. The <strong>first</strong> image (top-left in the grid) is the <strong>main</strong> storefront photo.</p>
                <ul id="product-images-sort" class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-4">
                    @foreach ($product->images as $image)
                        <li
                            data-id="{{ $image->id }}"
                            class="flex flex-col rounded-lg border border-slate-200 bg-white p-2 shadow-sm sm:p-3"
                        >
                            <div class="flex items-center justify-between gap-1">
                                <button type="button" class="drag-handle shrink-0 cursor-grab rounded p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 active:cursor-grabbing" title="Drag to reorder" aria-label="Drag to reorder">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 9h2v2H7V9zm4 0h2v2h-2V9zm-4 5h2v2H7v-2zm4 0h2v2h-2v-2z"/></svg>
                                </button>
                                <label class="flex cursor-pointer items-center gap-1.5 text-xs text-slate-600">
                                    <input type="checkbox" name="remove_image_ids[]" value="{{ $image->id }}" class="rounded border-slate-300 text-red-600 focus:ring-red-500">
                                    <span>Remove</span>
                                </label>
                            </div>
                            <div data-image-wrap class="relative mt-2 aspect-square w-full overflow-hidden rounded-md bg-slate-100">
                                <img src="{{ $image->url() }}" alt="" class="h-full w-full object-cover">
                                @if ($loop->first)
                                    <span data-main-badge class="absolute left-1 top-1 rounded bg-slate-900 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wide text-white">Main</span>
                                @endif
                            </div>
                            <label class="mt-2 block text-left">
                                <span class="text-[10px] font-medium text-slate-600">Color label (storefront swatch)</span>
                                <input
                                    type="text"
                                    name="image_color_label[{{ $image->id }}]"
                                    value="{{ old('image_color_label.'.$image->id, $image->color_label) }}"
                                    placeholder="e.g. Graphite"
                                    maxlength="64"
                                    class="mt-0.5 w-full rounded border border-slate-200 px-2 py-1 text-xs text-slate-900 placeholder:text-slate-400"
                                >
                            </label>
                            <p class="mt-1 truncate text-center text-[10px] text-slate-500">Image #{{ $image->id }}</p>
                        </li>
                    @endforeach
                </ul>
                <div id="image-order-fields" class="hidden" aria-hidden="true"></div>
            </div>
        @endif

        <div>
            <label for="images" class="block text-sm font-medium text-slate-700">Add images</label>
            <input type="file" name="images[]" id="images" multiple accept="image/jpeg,image/png,image/gif,image/webp,.webp,.jpg,.jpeg,.png,.gif,.bmp"
                class="mt-1 block w-full text-sm text-slate-600 file:mr-4 file:rounded-md file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-slate-800">
            <p class="mt-1 text-xs text-slate-500">JPEG, PNG, GIF, WebP, BMP — up to 10&nbsp;MB each. New images are added at the end; drag to move one to the top to make it main.</p>
            @error('images')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('image_order')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="new_image_color_labels_csv" class="block text-sm font-medium text-slate-700">Color labels for new uploads (optional)</label>
            <input
                type="text"
                name="new_image_color_labels_csv"
                id="new_image_color_labels_csv"
                value="{{ old('new_image_color_labels_csv') }}"
                placeholder="Graphite, Silver, Gold — same order as new files"
                class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
            >
            <p class="mt-1 text-xs text-slate-500">Comma-separated, matching the order of new files above. Leave blank to set labels after save.</p>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Save</button>
            <a href="{{ route('dashboard.products.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
        </div>
    </form>
@endsection

@if ($product->images->isNotEmpty())
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
        <script>
            (function () {
                var list = document.getElementById('product-images-sort');
                var form = document.getElementById('product-edit-form');
                var fields = document.getElementById('image-order-fields');
                if (!list || !form || !fields || typeof Sortable === 'undefined') return;

                var sortable = new Sortable(list, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'opacity-50',
                    onEnd: function () {
                        refreshMainBadges();
                    },
                });

                function refreshMainBadges() {
                    var items = list.querySelectorAll('li[data-id]');
                    items.forEach(function (li, i) {
                        var wrap = li.querySelector('[data-image-wrap]');
                        if (!wrap) return;
                        var badge = wrap.querySelector('[data-main-badge]');
                        if (i === 0) {
                            if (!badge) {
                                var span = document.createElement('span');
                                span.setAttribute('data-main-badge', '');
                                span.className = 'absolute left-1 top-1 rounded bg-slate-900 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wide text-white';
                                span.textContent = 'Main';
                                wrap.appendChild(span);
                            }
                        } else if (badge) {
                            badge.remove();
                        }
                    });
                }

                form.addEventListener('submit', function () {
                    fields.innerHTML = '';
                    var items = list.querySelectorAll('li[data-id]');
                    items.forEach(function (li) {
                        var id = li.getAttribute('data-id');
                        var cb = li.querySelector('input[name="remove_image_ids[]"]');
                        if (cb && cb.checked) return;
                        var input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'image_order[]';
                        input.value = id;
                        fields.appendChild(input);
                    });
                });
            })();
        </script>
    @endpush
@endif
