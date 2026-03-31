@extends('layouts.dashboard')

@section('title', 'New product — Admin')
@section('heading', 'New product')
@section('subheading', 'Slug is generated from the product name. Upload at least one image.')

@section('content')
    @if ($categories->isEmpty())
        <p class="mt-8 rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            <a href="{{ route('dashboard.categories.create') }}" class="font-medium underline">Create a category</a> before adding products.
        </p>
    @endif

    <form action="{{ route('dashboard.products.store') }}" method="post" enctype="multipart/form-data" class="mt-8 max-w-2xl space-y-5">
        @csrf
        <div>
            <label for="category_id" class="block text-sm font-medium text-slate-700">Category <span class="text-red-600">*</span></label>
            <select name="category_id" id="category_id" required @disabled($categories->isEmpty())
                class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                <option value="">Select category</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700">Name <span class="text-red-600">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
        </div>
        <div>
            <label for="description" class="block text-sm font-medium text-slate-700">Description</label>
            <textarea name="description" id="description" rows="4"
                class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">{{ old('description') }}</textarea>
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="price" class="block text-sm font-medium text-slate-700">Price (GHS) <span class="text-red-600">*</span></label>
                <input type="number" name="price" id="price" value="{{ old('price') }}" step="0.01" min="0" required
                    class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
            </div>
            <div>
                <label for="stock" class="block text-sm font-medium text-slate-700">Stock <span class="text-red-600">*</span></label>
                <input type="number" name="stock" id="stock" value="{{ old('stock', 0) }}" min="0" required
                    class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
            </div>
        </div>
        <div class="flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" id="is_active" value="1" class="rounded border-slate-300 text-slate-900 focus:ring-slate-500" @checked(old('is_active', true))>
            <label for="is_active" class="text-sm text-slate-700">Active (visible on storefront)</label>
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="discount_type" class="block text-sm font-medium text-slate-700">Discount type</label>
                <select name="discount_type" id="discount_type" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                    <option value="">None</option>
                    <option value="percent" @selected(old('discount_type') === 'percent')>Percent off</option>
                    <option value="fixed" @selected(old('discount_type') === 'fixed')>Fixed amount off</option>
                </select>
            </div>
            <div>
                <label for="discount_value" class="block text-sm font-medium text-slate-700">Discount value</label>
                <input type="number" name="discount_value" id="discount_value" value="{{ old('discount_value') }}" step="0.01" min="0" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                <p class="mt-1 text-xs text-slate-500">Percent: 1–99. Fixed: GHS off list price.</p>
            </div>
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div class="flex items-center gap-2">
                <input type="hidden" name="flash_sale" value="0">
                <input type="checkbox" name="flash_sale" id="flash_sale" value="1" class="rounded border-slate-300 text-slate-900 focus:ring-slate-500" @checked(old('flash_sale'))>
                <label for="flash_sale" class="text-sm text-slate-700">Flash sale</label>
            </div>
            <div>
                <label for="sale_end_time" class="block text-sm font-medium text-slate-700">Sale ends (optional)</label>
                <input type="datetime-local" name="sale_end_time" id="sale_end_time" value="{{ old('sale_end_time') }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
            </div>
        </div>
        <div class="flex flex-wrap gap-6">
            <div class="flex items-center gap-2">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" name="is_featured" id="is_featured" value="1" class="rounded border-slate-300 text-slate-900 focus:ring-slate-500" @checked(old('is_featured'))>
                <label for="is_featured" class="text-sm text-slate-700">Featured (home picks)</label>
            </div>
            <div class="flex items-center gap-2">
                <input type="hidden" name="is_trending" value="0">
                <input type="checkbox" name="is_trending" id="is_trending" value="1" class="rounded border-slate-300 text-slate-900 focus:ring-slate-500" @checked(old('is_trending'))>
                <label for="is_trending" class="text-sm text-slate-700">Trending badge</label>
            </div>
        </div>
        <div>
            <label for="images" class="block text-sm font-medium text-slate-700">Images <span class="text-red-600">*</span></label>
            <input type="file" name="images[]" id="images" multiple required accept="image/jpeg,image/png,image/gif,image/webp,.webp,.jpg,.jpeg,.png,.gif,.bmp"
                class="mt-1 block w-full text-sm text-slate-600 file:mr-4 file:rounded-md file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-slate-800">
            <p class="mt-1 text-xs text-slate-500">At least one image (JPEG, PNG, GIF, WebP, BMP). The <strong>first file</strong> you pick becomes the main photo; after saving you can reorder on the edit screen.</p>
            @error('images')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('images.*')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="image_color_labels_csv" class="block text-sm font-medium text-slate-700">Color labels (optional)</label>
            <input
                type="text"
                name="image_color_labels_csv"
                id="image_color_labels_csv"
                value="{{ old('image_color_labels_csv') }}"
                placeholder="Graphite, Pacific Blue, Silver, Gold — same order as images"
                class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
            >
            <p class="mt-1 text-xs text-slate-500">Comma-separated, one per image in upload order. Used as storefront color swatch names and tooltips.</p>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Create product</button>
            <a href="{{ route('dashboard.products.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
        </div>
    </form>
@endsection
