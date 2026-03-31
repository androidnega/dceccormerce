@extends('layouts.dashboard')

@section('title', 'Add news post — Admin')
@section('heading', 'Add news post')
@section('subheading', 'Appears in Popular news on the home page.')

@section('content')
    <form action="{{ route('dashboard.news-posts.store') }}" method="post" enctype="multipart/form-data" class="max-w-xl space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-medium text-neutral-800">Category</label>
            <input type="text" name="category" value="{{ old('category') }}" required maxlength="64" placeholder="e.g. Hi-Tech"
                class="mt-1 w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-[#0057b8] focus:outline-none focus:ring-2 focus:ring-[#0057b8]/25">
            @error('category')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-neutral-800">Headline</label>
            <input type="text" name="headline" value="{{ old('headline') }}" required maxlength="255"
                class="mt-1 w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-[#0057b8] focus:outline-none focus:ring-2 focus:ring-[#0057b8]/25">
            @error('headline')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-neutral-800">Published date</label>
            <input type="date" name="published_at" value="{{ old('published_at', now()->format('Y-m-d')) }}" required
                class="mt-1 w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-[#0057b8] focus:outline-none focus:ring-2 focus:ring-[#0057b8]/25">
            @error('published_at')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-neutral-800">Link URL</label>
            <input type="text" name="link_url" value="{{ old('link_url', '/products#store-search') }}"
                class="mt-1 w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-[#0057b8] focus:outline-none focus:ring-2 focus:ring-[#0057b8]/25"
                placeholder="/products#store-search or https://…">
            @error('link_url')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-neutral-800">Sort order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" max="9999"
                    class="mt-1 w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm text-neutral-900 shadow-sm">
            </div>
            <div class="flex items-end pb-1">
                <label class="flex cursor-pointer items-center gap-2 text-sm text-neutral-800">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-neutral-300 text-[#0057b8]" @checked(old('is_active', true))>
                    Active (show on store)
                </label>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-neutral-800">Image — upload</label>
            <input type="file" name="image" accept="image/*" class="mt-1 block w-full text-sm text-neutral-600 file:mr-3 file:rounded-lg file:border-0 file:bg-[#0057b8] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-[#00479a]">
            @error('image')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-neutral-800">Or image path (public)</label>
            <input type="text" name="image_path" value="{{ old('image_path') }}"
                class="mt-1 w-full rounded-lg border border-neutral-200 px-3 py-2 font-mono text-sm text-neutral-900 shadow-sm"
                placeholder="images/your-banner.webp">
            <p class="mt-1 text-xs text-neutral-500">Leave upload empty and set this to a file under <code class="rounded bg-neutral-100 px-1">public/</code>.</p>
            @error('image_path')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="flex flex-wrap gap-3">
            <button type="submit" class="rounded-lg bg-[#0057b8] px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#00479a]">Save post</button>
            <a href="{{ route('dashboard.news-posts.index') }}" class="rounded-lg border border-neutral-200 px-5 py-2.5 text-sm font-medium text-neutral-800 hover:bg-neutral-50">Cancel</a>
        </div>
    </form>
@endsection
