@extends('layouts.dashboard')

@section('title', 'Shop by category — Admin')
@section('heading', 'Shop by category')
@section('subheading', 'Edit the three large cards on the home page (images, titles, and the wide video card). The first three active rows by position are shown.')

@section('content')
    <div class="flex flex-wrap items-center justify-end gap-4">
        <a href="{{ route('dashboard.category-banners.create') }}" class="rounded-md bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800">Add card</a>
    </div>

    <div class="mt-8 overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-zinc-200 text-sm">
            <thead class="bg-zinc-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-zinc-900">Preview</th>
                    <th class="px-4 py-3 text-left font-semibold text-zinc-900">Title</th>
                    <th class="px-4 py-3 text-left font-semibold text-zinc-900">Type</th>
                    <th class="px-4 py-3 text-left font-semibold text-zinc-900">Position</th>
                    <th class="px-4 py-3 text-left font-semibold text-zinc-900">Active</th>
                    <th class="px-4 py-3 text-right font-semibold text-zinc-900">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse ($banners as $b)
                    <tr>
                        <td class="px-4 py-3">
                            @if ($b->type === \App\Models\CategoryBanner::TYPE_VIDEO)
                                <span class="inline-flex rounded-md border border-zinc-200 bg-zinc-100 px-2 py-1 text-xs font-medium text-zinc-600">Video</span>
                            @elseif ($b->imageUrl())
                                <div class="h-14 w-20 overflow-hidden rounded-md border border-zinc-200 bg-zinc-50">
                                    <img src="{{ $b->imageUrl() }}" alt="" class="h-full w-full object-contain object-center">
                                </div>
                            @else
                                <span class="text-xs text-zinc-400">No image</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-zinc-800">
                            <div class="font-medium text-zinc-900">{{ $b->title }}</div>
                            @if (filled($b->subtitle))<div class="mt-0.5 text-xs text-zinc-500">{{ $b->subtitle }}</div>@endif
                        </td>
                        <td class="px-4 py-3 text-zinc-600">
                            @if ($b->type === \App\Models\CategoryBanner::TYPE_VIDEO)
                                Video
                            @else
                                Image
                            @endif
                        </td>
                        <td class="px-4 py-3 text-zinc-600 tabular-nums">{{ $b->position }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs {{ $b->active ? 'bg-emerald-50 text-emerald-700' : 'bg-zinc-100 text-zinc-600' }}">
                                {{ $b->active ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('dashboard.category-banners.edit', $b) }}" class="text-zinc-700 underline hover:text-zinc-900">Edit</a>
                            <form action="{{ route('dashboard.category-banners.destroy', $b) }}" method="post" class="inline pl-3" onsubmit="return confirm('Delete this card?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-zinc-500">No cards yet. Add up to three (or more — only the first three active by position show on the home page).</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
