@extends('layouts.dashboard')

@section('title', 'New hero slide — Admin')
@section('heading', 'New hero slide')
@section('subheading', 'Upload an image and set copy. Link the button to a product or URL.')

@section('content')
    <form action="{{ route('dashboard.hero-slides.store') }}" method="post" enctype="multipart/form-data" class="mt-0 max-w-2xl space-y-5">
        @csrf
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="sort_order" class="block text-sm font-medium text-neutral-700">Sort order</label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" min="0" max="9999"
                    class="mt-1 w-full rounded-md border border-neutral-300 px-3 py-2 text-neutral-900 shadow-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500">
            </div>
            <div class="flex items-end pb-2">
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" class="rounded border-neutral-300 text-neutral-900 focus:ring-neutral-500" @checked(old('is_active', true))>
                    <label for="is_active" class="text-sm text-neutral-700">Active</label>
                </div>
            </div>
        </div>
        <div>
            <label for="subheading" class="block text-sm font-medium text-neutral-700">Subheading <span class="text-red-600">*</span></label>
            <input type="text" name="subheading" id="subheading" value="{{ old('subheading') }}" required
                class="mt-1 w-full rounded-md border border-neutral-300 px-3 py-2 text-neutral-900 shadow-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500"
                placeholder="The world's largest">
        </div>
        <div>
            <label for="headline" class="block text-sm font-medium text-neutral-700">Main headline <span class="text-red-600">*</span></label>
            <input type="text" name="headline" id="headline" value="{{ old('headline') }}" required
                class="mt-1 w-full rounded-md border border-neutral-300 px-3 py-2 text-neutral-900 shadow-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500"
                placeholder="Apple Store">
        </div>
        <div>
            <label for="headline_line2" class="block text-sm font-medium text-neutral-700">Second headline line (optional)</label>
            <input type="text" name="headline_line2" id="headline_line2" value="{{ old('headline_line2') }}"
                class="mt-1 w-full rounded-md border border-neutral-300 px-3 py-2 text-neutral-900 shadow-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500">
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="cta_label" class="block text-sm font-medium text-neutral-700">Button label</label>
                <input type="text" name="cta_label" id="cta_label" value="{{ old('cta_label', 'Shop now') }}"
                    class="mt-1 w-full rounded-md border border-neutral-300 px-3 py-2 text-neutral-900 shadow-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500">
            </div>
            <div>
                <label for="product_id" class="block text-sm font-medium text-neutral-700">Link to product</label>
                <select name="product_id" id="product_id"
                    class="mt-1 w-full rounded-md border border-neutral-300 px-3 py-2 text-neutral-900 shadow-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500">
                    <option value="">— None —</option>
                    @foreach ($products as $p)
                        <option value="{{ $p->id }}" @selected(old('product_id') == $p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label for="cta_url" class="block text-sm font-medium text-neutral-700">Custom URL (if no product)</label>
            <input type="text" name="cta_url" id="cta_url" value="{{ old('cta_url') }}"
                class="mt-1 w-full rounded-md border border-neutral-300 px-3 py-2 text-neutral-900 shadow-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500"
                placeholder="https://… or /path">
            <p class="mt-1 text-xs text-neutral-500">Used when no product is selected.</p>
        </div>
        <div>
            <label for="background_hex" class="block text-sm font-medium text-neutral-700">Slide background</label>
            <input type="color" name="background_hex" id="background_hex" value="{{ old('background_hex', '#f1f5f9') }}" class="mt-1 h-10 w-full max-w-[12rem] cursor-pointer rounded border border-neutral-300 bg-white p-1">
            <p class="mt-1 text-xs text-neutral-500">Panel background for this slide. Applies to the Demo homepage layout (categories + hero); each slide can use its own color.</p>
        </div>
        <div>
            <label for="image" class="block text-sm font-medium text-neutral-700">Image <span class="text-red-600">*</span></label>
            <input type="file" name="image" id="image" required accept="image/*"
                class="mt-1 block w-full cursor-pointer rounded-lg border border-slate-300 bg-white px-2 py-2 text-sm text-slate-800 shadow-sm file:mr-4 file:cursor-pointer file:rounded-md file:border-0 file:bg-primary-600 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-white hover:file:bg-primary-700">
        </div>
        <div class="flex gap-3">
            <button type="submit" class="rounded-md bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm ring-1 ring-primary-700/30 hover:bg-primary-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2">Create slide</button>
            <a href="{{ route('dashboard.hero-slides.index') }}" class="rounded-md border border-neutral-300 px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">Cancel</a>
        </div>
    </form>
@endsection
