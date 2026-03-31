@extends('layouts.dashboard')

@section('title', 'Promos — Admin')
@section('heading', 'Promos')
@section('subheading', 'Two homepage placements: upper Spotlight strip and lower More offers grid. Cart discount % still applies at checkout.')

@section('content')
    <div class="flex flex-wrap items-center justify-end gap-4">
        <a href="{{ route('dashboard.promos.create') }}" class="rounded-lg border border-zinc-200 bg-white px-4 py-2.5 text-sm font-medium text-zinc-900 shadow-sm hover:bg-zinc-50">Add promo</a>
    </div>

    @if ($promos->isEmpty())
        <p class="mt-10 rounded-xl border border-dashed border-neutral-200 bg-neutral-50 px-6 py-12 text-center text-sm text-neutral-600">
            No promos yet. <a href="{{ route('dashboard.promos.create') }}" class="font-medium text-neutral-900 underline">Create one</a>.
        </p>
    @else
        <div class="mt-8 overflow-x-auto rounded-xl border border-neutral-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-neutral-200 text-sm">
                <thead class="bg-neutral-50 text-left text-xs font-semibold uppercase tracking-wide text-neutral-500">
                    <tr>
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Home</th>
                        <th class="px-4 py-3">Value</th>
                        <th class="px-4 py-3">Order</th>
                        <th class="px-4 py-3">Active</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @foreach ($promos as $promo)
                        <tr class="hover:bg-neutral-50/80">
                            <td class="px-4 py-3 font-medium text-neutral-900">{{ $promo->title }}</td>
                            <td class="px-4 py-3 text-neutral-600">{{ $promo->type }}</td>
                            <td class="px-4 py-3 text-neutral-600">{{ ($promo->homepage_slot ?? 'primary') === 'secondary' ? 'Upper strip' : 'Lower grid' }}</td>
                            <td class="max-w-xs truncate px-4 py-3 text-neutral-600" title="{{ $promo->value }}">{{ $promo->value !== '' ? $promo->value : '—' }}</td>
                            <td class="px-4 py-3 tabular-nums text-neutral-600">{{ $promo->sort_order }}</td>
                            <td class="px-4 py-3">{{ $promo->is_active ? 'Yes' : 'No' }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('dashboard.promos.edit', $promo) }}" class="font-medium text-[#0057b8] underline decoration-[#cce0f7] underline-offset-2">Edit</a>
                                <form action="{{ route('dashboard.promos.destroy', $promo) }}" method="post" class="ml-3 inline" onsubmit="return confirm('Delete this promo?');">
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
        <div class="mt-6">{{ $promos->links() }}</div>
    @endif
@endsection
