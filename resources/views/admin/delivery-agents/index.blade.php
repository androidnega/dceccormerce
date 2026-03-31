@extends('layouts.dashboard')

@section('title', 'Delivery agents — Admin')
@section('heading', 'Delivery agents')
@section('subheading', 'Riders, drivers, and third-party partners available for assignment.')

@section('content')
    <div class="flex flex-wrap items-center justify-end gap-4">
        <a href="{{ route('dashboard.delivery-agents.create') }}" class="rounded-lg border border-zinc-200 bg-white px-4 py-2.5 text-sm font-medium text-zinc-900 shadow-sm hover:bg-zinc-50">Add agent</a>
    </div>

    @if ($agents->isEmpty())
        <p class="mt-10 rounded-xl border border-dashed border-neutral-200 bg-neutral-50 px-6 py-12 text-center text-sm text-neutral-600">
            No agents yet. <a href="{{ route('dashboard.delivery-agents.create') }}" class="font-medium text-neutral-900 underline">Create one</a> or run the delivery agent seeder to mirror riders.
        </p>
    @else
        <div class="mt-8 overflow-x-auto rounded-xl border border-neutral-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-neutral-200 text-sm">
                <thead class="bg-neutral-50 text-left text-xs font-semibold uppercase tracking-wide text-neutral-500">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Phone</th>
                        <th class="px-4 py-3">Vehicle</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Linked rider</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @foreach ($agents as $agent)
                        <tr class="hover:bg-neutral-50/80">
                            <td class="px-4 py-3 font-medium text-neutral-900">{{ $agent->name }}</td>
                            <td class="px-4 py-3 text-neutral-700">{{ $agent->type }}</td>
                            <td class="px-4 py-3 text-neutral-700">{{ $agent->phone ?: '—' }}</td>
                            <td class="px-4 py-3 text-neutral-700">{{ $agent->vehicle_type ?: '—' }}</td>
                            <td class="px-4 py-3 text-neutral-700">{{ $agent->status }}</td>
                            <td class="px-4 py-3 text-neutral-600">{{ $agent->rider ? $agent->rider->name : '—' }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('dashboard.delivery-agents.edit', $agent) }}" class="font-medium text-[#0057b8] underline decoration-[#cce0f7] underline-offset-2">Edit</a>
                                <form action="{{ route('dashboard.delivery-agents.destroy', $agent) }}" method="post" class="ml-3 inline" onsubmit="return confirm('Delete this agent?');">
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
        <div class="mt-6">{{ $agents->links() }}</div>
    @endif
@endsection
