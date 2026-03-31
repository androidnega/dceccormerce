@extends('layouts.dashboard')

@section('title', 'Product display — Admin')
@section('heading', 'Product display')
@section('subheading', 'Storefront card behavior, layout, and interactions.')

@section('content')
    <form action="{{ route('dashboard.store-product-display.update') }}" method="post" class="admin-form max-w-3xl space-y-8">
        @csrf
        @method('PUT')

        <div class="rounded-2xl border border-[#cce0f7] bg-[#e6f0fb] p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between sm:gap-6">
                <div class="max-w-xl space-y-2">
                    <p class="text-sm leading-relaxed text-slate-800">
                        Controls how product cards render on the shop catalog. Updates apply on the storefront <span class="font-semibold text-slate-900">after you save</span>—this page does <span class="font-semibold text-slate-900">not</span> auto-save when you click options.
                    </p>
                </div>
            </div>
        </div>

        <fieldset class="space-y-4 rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm ring-1 ring-black/[0.03]">
            <legend class="flex items-center gap-2 text-base font-semibold text-neutral-900">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600"><i class="fa-solid fa-border-all" aria-hidden="true"></i></span>
                Layout
            </legend>
            <p class="text-sm text-neutral-600">Default listing layout on the products page (grid, list, carousel, masonry, compact, or sleek cards).</p>
            <div class="grid gap-3 sm:grid-cols-2">
                @foreach (\App\Models\StoreProductDisplaySetting::layoutOptions() as $layout)
                    <label class="flex cursor-pointer gap-3 rounded-xl border border-neutral-200 bg-neutral-50/80 p-4 has-[:checked]:border-[#0057b8] has-[:checked]:bg-white has-[:checked]:ring-1 has-[:checked]:ring-[#0057b8]">
                        <input type="radio" name="product_layout" value="{{ $layout }}" class="mt-1 text-[#0057b8]" @checked(old('product_layout', $settings->product_layout) === $layout)>
                        <div>
                            <span class="font-medium capitalize text-neutral-900">{{ str_replace('_', ' ', $layout) }}</span>
                            <p class="mt-1 text-xs text-neutral-500">
                                @switch($layout)
                                    @case('grid') Standard responsive columns @break
                                    @case('list') Horizontal rows @break
                                    @case('carousel') Horizontal scrolling strip @break
                                    @case('masonry') CSS columns / staggered @break
                                    @case('compact') Denser cards @break
                                    @case('sleek') Clean white cards, square photo, price + heart, star row, title (marketplace style) @break
                                @endswitch
                            </p>
                        </div>
                    </label>
                @endforeach
            </div>
            @error('product_layout')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </fieldset>

        <fieldset class="space-y-5 rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm ring-1 ring-black/[0.03]">
            <legend class="flex items-center gap-2 text-base font-semibold text-neutral-900">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600"><i class="fa-solid fa-sliders" aria-hidden="true"></i></span>
                Card size
            </legend>
            <div class="flex flex-wrap gap-4">
                @foreach (\App\Models\StoreProductDisplaySetting::cardSizeOptions() as $size)
                    <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-neutral-200 px-4 py-2 has-[:checked]:border-[#0057b8] has-[:checked]:bg-[#e6f0fb]">
                        <input type="radio" name="card_size" value="{{ $size }}" class="text-[#0057b8]" @checked(old('card_size', $settings->card_size) === $size)>
                        <span class="capitalize text-sm font-medium text-neutral-900">{{ $size }}</span>
                    </label>
                @endforeach
            </div>
            @error('card_size')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </fieldset>

        <fieldset class="space-y-4 rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm ring-1 ring-black/[0.03]">
            <legend class="flex items-center gap-2 text-base font-semibold text-neutral-900">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600"><i class="fa-solid fa-star" aria-hidden="true"></i></span>
                Featured products (products page)
            </legend>
            <p class="text-sm text-neutral-600">How the &ldquo;Featured products&rdquo; block appears above the main catalog grid.</p>
            <div class="grid gap-3 sm:grid-cols-1">
                @foreach (\App\Models\StoreProductDisplaySetting::featuredProductsDisplayOptions() as $opt)
                    <label class="flex cursor-pointer gap-3 rounded-xl border border-neutral-200 bg-neutral-50/80 p-4 has-[:checked]:border-[#0057b8] has-[:checked]:bg-white has-[:checked]:ring-1 has-[:checked]:ring-[#0057b8]">
                        <input
                            type="radio"
                            name="featured_products_display"
                            value="{{ $opt }}"
                            class="mt-1 text-[#0057b8]"
                            @checked(old('featured_products_display', $settings->featured_products_display ?? \App\Models\StoreProductDisplaySetting::FEATURED_GRID) === $opt)
                        >
                        <div>
                            <span class="font-medium capitalize text-neutral-900">{{ str_replace('_', ' ', $opt) }}</span>
                            <p class="mt-1 text-xs text-neutral-500">
                                @switch($opt)
                                    @case('grid') At least four cards per row on large screens; clean grid. @break
                                    @case('carousel') Horizontal scrolling strip (same as before). @break
                                    @case('showcase') Large hero-style slideshow with arrows, dots, and soft gradient (Apple-like). @break
                                @endswitch
                            </p>
                        </div>
                    </label>
                @endforeach
            </div>
            @error('featured_products_display')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </fieldset>

        <fieldset class="space-y-4 rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm ring-1 ring-black/[0.03]">
            <legend class="flex items-center gap-2 text-base font-semibold text-neutral-900">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600"><i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i></span>
                Interactions
            </legend>

            @php
                $toggles = [
                    'enable_hover_actions' => ['label' => 'Hover actions', 'help' => 'Overlay with quick actions on card hover (desktop).'],
                    'enable_quick_view' => ['label' => 'Quick view', 'help' => 'Opens a modal with product details without leaving the listing.'],
                    'enable_wishlist' => ['label' => 'Wishlist', 'help' => 'Heart button on cards (uses session wishlist).'],
                    'enable_image_hover_swap' => ['label' => 'Image hover swap', 'help' => 'If a second image exists, swap on hover.'],
                ];
            @endphp

            @foreach ($toggles as $field => $meta)
                <label class="flex cursor-pointer items-start gap-4 rounded-xl border border-neutral-100 bg-neutral-50/80 p-4 transition hover:border-indigo-200">
                    <input
                        type="checkbox"
                        name="{{ $field }}"
                        value="1"
                        class="mt-1 h-5 w-5 shrink-0 rounded border-neutral-300 text-[#0057b8] focus:ring-[#0057b8]"
                        @checked(old($field, $settings->{$field}))
                    >
                    <div>
                        <span class="font-medium text-neutral-900">{{ $meta['label'] }}</span>
                        <p class="mt-1 text-sm text-neutral-500">{{ $meta['help'] }}</p>
                    </div>
                </label>
            @endforeach
        </fieldset>

        <div class="sticky bottom-0 z-10 flex flex-wrap gap-3 border-t border-[#cce0f7] bg-[#e6f0fb] py-4">
            <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-[#0057b8] px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#00479a]">
                <i class="fa-solid fa-floppy-disk mr-2 text-xs opacity-90" aria-hidden="true"></i>
                Save settings
            </button>
            <a href="{{ route('dashboard.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-6 py-2.5 text-sm font-medium text-slate-800 shadow-sm hover:bg-slate-50">Cancel</a>
        </div>
    </form>
@endsection
