@extends('layouts.dashboard')

@section('title', 'Products — Admin')
@section('heading', 'Products')

@section('content')
    <div class="flex flex-wrap items-center justify-end gap-4">
        <a href="{{ route('dashboard.products.create') }}" class="rounded-md bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800">New product</a>
    </div>

    <div class="mt-8 overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-900">Image</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-900">Name</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-900">Category</th>
                    <th class="px-4 py-3 text-right font-semibold text-slate-900">Price (GHS)</th>
                    <th class="px-4 py-3 text-right font-semibold text-slate-900">Stock</th>
                    <th class="px-4 py-3 text-center font-semibold text-slate-900">Active</th>
                    <th class="px-4 py-3 text-right font-semibold text-slate-900">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($products as $product)
                    @php($thumb = $product->images->first())
                    <tr>
                        <td class="px-4 py-2">
                            @if ($thumb)
                                <img src="{{ $thumb->url() }}" alt="" class="h-12 w-12 rounded object-cover">
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $product->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $product->category->name }}</td>
                        <td class="px-4 py-3 text-right text-slate-800">{{ format_ghs($product->price) }}</td>
                        <td class="px-4 py-3 text-right text-slate-800">{{ $product->stock }}</td>
                        <td class="px-4 py-3 text-center">
                            @if ($product->is_active)
                                <span class="text-emerald-700">Yes</span>
                            @else
                                <span class="text-slate-500">No</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('dashboard.products.edit', $product) }}" class="text-slate-700 underline hover:text-slate-900">Edit</a>
                            <form action="{{ route('dashboard.products.destroy', $product) }}" method="post" class="inline pl-3" onsubmit="return confirm('Delete this product?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-slate-500">No products yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $products->links() }}
    </div>
@endsection
