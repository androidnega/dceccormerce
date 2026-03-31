@extends('layouts.dashboard')

@section('title', 'Hero slides — Admin')
@section('heading', 'Slideshow')
@section('subheading', 'Hero images shown on the storefront homepage.')

@section('content')
    <div class="flex flex-wrap items-center justify-end gap-4">
        <a href="{{ route('dashboard.hero-slides.create') }}" class="rounded-lg border border-zinc-200 bg-white px-4 py-2.5 text-sm font-medium text-zinc-900 shadow-sm hover:bg-zinc-50">Add slide</a>
    </div>

    <p class="mt-4 max-w-3xl text-sm text-zinc-600">If no slides exist here, the store uses built-in demo slides until you run <code class="rounded bg-zinc-100 px-1 text-xs">php artisan db:seed --class=HeroSlideSeeder</code>.</p>

    @if ($slides->isEmpty())
        <p class="mt-10 rounded-xl border border-dashed border-neutral-200 bg-neutral-50 px-6 py-12 text-center text-sm text-neutral-600">
            No slides in the database yet. <a href="{{ route('dashboard.hero-slides.create') }}" class="font-medium text-neutral-900 underline">Add a slide</a>, or seed the default three (MacBook, iPad, Apple&nbsp;TV) with
            <code class="rounded bg-white px-1.5 py-0.5 text-xs">php artisan db:seed --class=HeroSlideSeeder</code>
            — then refresh this page to edit them here.
        </p>
    @else
        <div class="mt-10 overflow-x-auto rounded-xl border border-neutral-200">
            <table class="min-w-full divide-y divide-neutral-200 text-left text-sm">
                <thead class="bg-neutral-50 text-xs font-semibold uppercase tracking-wide text-neutral-500">
                    <tr>
                        <th class="px-4 py-3">Order</th>
                        <th class="px-4 py-3">Headline</th>
                        <th class="px-4 py-3">Sub</th>
                        <th class="px-4 py-3">Active</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 bg-white">
                    @foreach ($slides as $slide)
                        <tr>
                            <td class="whitespace-nowrap px-4 py-3 text-neutral-600">{{ $slide->sort_order }}</td>
                            <td class="max-w-xs px-4 py-3 font-medium text-neutral-900">{{ $slide->headline }}</td>
                            <td class="max-w-xs truncate px-4 py-3 text-neutral-600">{{ $slide->subheading }}</td>
                            <td class="px-4 py-3">{{ $slide->is_active ? 'Yes' : 'No' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                <a href="{{ route('dashboard.hero-slides.edit', $slide) }}" class="text-neutral-700 underline hover:text-neutral-900">Edit</a>
                                <form action="{{ route('dashboard.hero-slides.destroy', $slide) }}" method="post" class="ml-3 inline" onsubmit="return confirm('Delete this slide?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 underline hover:text-red-800">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <p class="mt-8 text-sm text-neutral-500">
        <a href="{{ route('dashboard.homepage-settings.edit') }}" class="font-medium text-neutral-900 underline">Homepage layout</a>
        ·
        <a href="{{ route('dashboard.index') }}" class="underline hover:text-neutral-900">Admin home</a>
    </p>
@endsection
