@extends('layouts.dashboard')

@section('title', 'Categories — Admin')
@section('heading', 'Categories')

@section('content')
    <div class="flex flex-wrap items-center justify-end gap-4">
        <a href="{{ route('dashboard.categories.create') }}" class="rounded-md bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800">New category</a>
    </div>

    <div class="mt-8 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-900">Name</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-900">Slug</th>
                    <th class="px-4 py-3 text-right font-semibold text-slate-900">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($categories as $category)
                    <tr>
                        <td class="px-4 py-3 text-slate-800">{{ $category->name }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $category->slug }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('dashboard.categories.edit', $category) }}" class="text-slate-700 underline hover:text-slate-900">Edit</a>
                            <form action="{{ route('dashboard.categories.destroy', $category) }}" method="post" class="inline pl-3" onsubmit="return confirm('Delete this category?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-slate-500">No categories yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $categories->links() }}
    </div>
@endsection
