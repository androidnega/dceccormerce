@extends('layouts.dashboard')

@section('title', 'Homepage sections — Admin')
@section('heading', 'Homepage sections')
@section('subheading', 'Order blocks on the store home page (below the hero). Lower numbers appear first.')

@section('content')
    <div class="flex flex-wrap items-center justify-end gap-4">
        <a href="{{ route('dashboard.homepage-sections.create') }}" class="rounded-lg border border-zinc-200 bg-white px-4 py-2.5 text-sm font-medium text-zinc-900 shadow-sm hover:bg-zinc-50">Add section</a>
    </div>

    <p class="mt-4 max-w-3xl text-sm text-zinc-600">
        Types: <strong class="font-medium text-zinc-800">slider</strong> (announcement strip or banner),
        <strong class="font-medium text-zinc-800">featured_promo</strong>,
        <strong class="font-medium text-zinc-800">product_grid</strong>,
        <strong class="font-medium text-zinc-800">soft_promo</strong>,
        <strong class="font-medium text-zinc-800">flash_section</strong> (trending row).
        The <strong class="font-medium text-zinc-800">Shop by category</strong> row is managed separately under
        <a href="{{ route('dashboard.category-banners.index') }}" class="font-medium text-zinc-900 underline">Shop by category</a>.
    </p>

    @if ($sections->isEmpty())
        <p class="mt-10 rounded-xl border border-dashed border-neutral-200 bg-neutral-50 px-6 py-12 text-center text-sm text-neutral-600">
            No sections yet. <a href="{{ route('dashboard.homepage-sections.create') }}" class="font-medium text-neutral-900 underline">Add your first section</a>
            or run <code class="rounded bg-neutral-100 px-1 font-mono text-xs">php artisan db:seed --class=HomepageSectionSeeder</code>.
        </p>
    @else
        <div class="mt-8 overflow-x-auto rounded-xl border border-neutral-200 bg-white">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-neutral-200 bg-neutral-50 text-xs font-semibold uppercase tracking-wide text-neutral-600">
                    <tr>
                        <th class="px-4 py-3">Pos</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">Active</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @foreach ($sections as $section)
                        <tr class="hover:bg-neutral-50/80">
                            <td class="px-4 py-3 font-mono text-neutral-700">{{ $section->position }}</td>
                            <td class="px-4 py-3 text-neutral-900">{{ $section->type }}</td>
                            <td class="px-4 py-3 text-neutral-700">{{ \Illuminate\Support\Str::limit($section->title ?? '—', 48) }}</td>
                            <td class="px-4 py-3">{{ $section->is_active ? 'Yes' : 'No' }}</td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('dashboard.homepage-sections.edit', $section) }}" class="font-medium text-[#0057b8] underline decoration-[#cce0f7] underline-offset-2">Edit</a>
                                <form action="{{ route('dashboard.homepage-sections.destroy', $section) }}" method="post" class="ms-3 inline" onsubmit="return confirm('Delete this section?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-medium text-red-600 underline hover:text-red-800">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <p class="mt-10 text-sm text-neutral-500">
        <a href="{{ route('dashboard.index') }}" class="font-medium text-neutral-900 underline">Admin overview</a>
        ·
        <a href="{{ route('home') }}" class="underline hover:text-neutral-900">View storefront</a>
    </p>
@endsection
