@extends('layouts.dashboard')

@section('title', 'Edit hero slide — Admin')
@section('heading', 'Edit hero slide')

@section('content')
    <form action="{{ route('dashboard.hero-slides.update', $heroSlide) }}" method="post" enctype="multipart/form-data" class="mt-0 max-w-2xl space-y-5">
        @csrf
        @method('PUT')
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="sort_order" class="block text-sm font-medium text-neutral-700">Sort order</label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $heroSlide->sort_order) }}" min="0" max="9999"
                    class="mt-1 w-full rounded-md border border-neutral-300 px-3 py-2 text-neutral-900 shadow-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500">
            </div>
            <div class="flex items-end pb-2">
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" class="rounded border-neutral-300 text-neutral-900 focus:ring-neutral-500" @checked(old('is_active', $heroSlide->is_active))>
                    <label for="is_active" class="text-sm text-neutral-700">Active</label>
                </div>
            </div>
        </div>
        <div>
            <label for="subheading" class="block text-sm font-medium text-neutral-700">Subheading <span class="text-red-600">*</span></label>
            <input type="text" name="subheading" id="subheading" value="{{ old('subheading', $heroSlide->subheading) }}" required
                class="mt-1 w-full rounded-md border border-neutral-300 px-3 py-2 text-neutral-900 shadow-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500">
        </div>
        <div>
            <label for="headline" class="block text-sm font-medium text-neutral-700">Main headline <span class="text-red-600">*</span></label>
            <input type="text" name="headline" id="headline" value="{{ old('headline', $heroSlide->headline) }}" required
                class="mt-1 w-full rounded-md border border-neutral-300 px-3 py-2 text-neutral-900 shadow-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500">
        </div>
        <div>
            <label for="headline_line2" class="block text-sm font-medium text-neutral-700">Second headline line (optional)</label>
            <input type="text" name="headline_line2" id="headline_line2" value="{{ old('headline_line2', $heroSlide->headline_line2) }}"
                class="mt-1 w-full rounded-md border border-neutral-300 px-3 py-2 text-neutral-900 shadow-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500">
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="cta_label" class="block text-sm font-medium text-neutral-700">Button label</label>
                <input type="text" name="cta_label" id="cta_label" value="{{ old('cta_label', $heroSlide->cta_label) }}"
                    class="mt-1 w-full rounded-md border border-neutral-300 px-3 py-2 text-neutral-900 shadow-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500">
            </div>
            <div>
                <label for="product_id" class="block text-sm font-medium text-neutral-700">Link to product</label>
                <select name="product_id" id="product_id"
                    class="mt-1 w-full rounded-md border border-neutral-300 px-3 py-2 text-neutral-900 shadow-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500">
                    <option value="">— None —</option>
                    @foreach ($products as $p)
                        <option value="{{ $p->id }}" @selected(old('product_id', $heroSlide->product_id) == $p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label for="cta_url" class="block text-sm font-medium text-neutral-700">Custom URL (if no product)</label>
            <input type="text" name="cta_url" id="cta_url" value="{{ old('cta_url', $heroSlide->cta_url) }}"
                class="mt-1 w-full rounded-md border border-neutral-300 px-3 py-2 text-neutral-900 shadow-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500">
        </div>
        <div>
            <label for="background_hex" class="block text-sm font-medium text-neutral-700">Slide background</label>
            <input type="color" name="background_hex" id="background_hex" value="{{ old('background_hex', $heroSlide->background_hex ?? '#f1f5f9') }}" class="mt-1 h-10 w-full max-w-[12rem] cursor-pointer rounded border border-neutral-300 bg-white p-1">
            <p class="mt-1 text-xs text-neutral-500">Panel background for this slide. Applies to the Demo homepage layout (categories + hero); each slide can use its own color.</p>
        </div>
        @if ($heroSlide->image_path)
            <div class="rounded-lg border border-neutral-200 bg-neutral-50 p-4">
                <p class="text-xs font-medium text-neutral-500">Current image</p>
                <img src="{{ $heroSlide->imageUrl() }}" alt="" class="mt-2 max-h-40 rounded object-contain">
            </div>
        @endif
        <div>
            <label for="image" class="block text-sm font-medium text-neutral-700">Replace image</label>
            <input type="file" name="image" id="image" accept="image/*"
                class="mt-1 block w-full cursor-pointer rounded-lg border border-slate-300 bg-white px-2 py-2 text-sm text-slate-800 shadow-sm file:mr-4 file:cursor-pointer file:rounded-md file:border-0 file:bg-primary-600 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-white hover:file:bg-primary-700">
            <p class="mt-1 text-xs text-neutral-500">Leave empty to keep the current image.</p>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="rounded-md bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm ring-1 ring-primary-700/30 hover:bg-primary-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2">Save</button>
            <a href="{{ route('dashboard.hero-slides.index') }}" class="rounded-md border border-neutral-300 px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">Cancel</a>
        </div>
    </form>
@endsection
