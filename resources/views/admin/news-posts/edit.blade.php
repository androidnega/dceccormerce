@extends('layouts.dashboard')

@section('title', 'Edit news post — Admin')
@section('heading', 'Edit news post')
@section('subheading', $newsPost->headline)

@section('content')
    <form action="{{ route('dashboard.news-posts.update', $newsPost) }}" method="post" enctype="multipart/form-data" class="max-w-xl space-y-5">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-medium text-neutral-800">Category</label>
            <input type="text" name="category" value="{{ old('category', $newsPost->category) }}" required maxlength="64"
                class="mt-1 w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-[#0057b8] focus:outline-none focus:ring-2 focus:ring-[#0057b8]/25">
            @error('category')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-neutral-800">Headline</label>
            <input type="text" name="headline" value="{{ old('headline', $newsPost->headline) }}" required maxlength="255"
                class="mt-1 w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-[#0057b8] focus:outline-none focus:ring-2 focus:ring-[#0057b8]/25">
            @error('headline')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-neutral-800">Published date</label>
            <input type="date" name="published_at" value="{{ old('published_at', $newsPost->published_at->format('Y-m-d')) }}" required
                class="mt-1 w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-[#0057b8] focus:outline-none focus:ring-2 focus:ring-[#0057b8]/25">
            @error('published_at')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-neutral-800">Link URL</label>
            <input type="text" name="link_url" value="{{ old('link_url', $newsPost->link_url) }}"
                class="mt-1 w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-[#0057b8] focus:outline-none focus:ring-2 focus:ring-[#0057b8]/25">
            @error('link_url')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-neutral-800">Sort order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $newsPost->sort_order) }}" min="0" max="9999"
                    class="mt-1 w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm text-neutral-900 shadow-sm">
            </div>
            <div class="flex items-end pb-1">
                <label class="flex cursor-pointer items-center gap-2 text-sm text-neutral-800">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-neutral-300 text-[#0057b8]" @checked(old('is_active', $newsPost->is_active))>
                    Active
                </label>
            </div>
        </div>
        <div class="rounded-lg border border-neutral-200 bg-neutral-50 p-3">
            <p class="text-xs font-medium text-neutral-600">Current image</p>
            <div class="mt-2 aspect-[16/10] max-w-xs overflow-hidden rounded-md border border-neutral-200 bg-white">
                <img src="{{ $newsPost->resolveImageUrl() }}" alt="" class="h-full w-full object-cover">
            </div>
            <p class="mt-2 font-mono text-xs text-neutral-500">{{ $newsPost->image_path }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-neutral-800">Replace image (upload)</label>
            <input type="file" name="image" accept="image/*" class="mt-1 block w-full text-sm text-neutral-600 file:mr-3 file:rounded-lg file:border-0 file:bg-[#0057b8] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-[#00479a]">
            @error('image')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-neutral-800">Or set path (public)</label>
            <input type="text" name="image_path" value="{{ old('image_path', $newsPost->image_path) }}"
                class="mt-1 w-full rounded-lg border border-neutral-200 px-3 py-2 font-mono text-sm text-neutral-900 shadow-sm">
            @error('image_path')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="flex flex-wrap gap-3">
            <button type="submit" class="rounded-lg bg-[#0057b8] px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#00479a]">Save changes</button>
            <a href="{{ route('dashboard.news-posts.index') }}" class="rounded-lg border border-neutral-200 px-5 py-2.5 text-sm font-medium text-neutral-800 hover:bg-neutral-50">Back</a>
        </div>
    </form>
@endsection
