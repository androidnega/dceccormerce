@extends('layouts.dashboard')

@section('title', 'Delivery rules — Admin')
@section('heading', 'Delivery rules')
@section('subheading', 'Zone, method, and option combinations set checkout prices and fulfillment methods.')

@section('content')
    <div class="flex flex-wrap items-center justify-end gap-4">
        <a href="{{ route('dashboard.delivery-rules.create') }}" class="rounded-lg border border-zinc-200 bg-white px-4 py-2.5 text-sm font-medium text-zinc-900 shadow-sm hover:bg-zinc-50">Add rule</a>
    </div>

    @if ($rules->isEmpty())
        <p class="mt-10 rounded-xl border border-dashed border-neutral-200 bg-neutral-50 px-6 py-12 text-center text-sm text-neutral-600">
            No rules yet. <a href="{{ route('dashboard.delivery-rules.create') }}" class="font-medium text-neutral-900 underline">Create one</a> or run the delivery rule seeder.
        </p>
    @else
        <div class="mt-8 overflow-x-auto rounded-xl border border-neutral-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-neutral-200 text-sm">
                <thead class="bg-neutral-50 text-left text-xs font-semibold uppercase tracking-wide text-neutral-500">
                    <tr>
                        <th class="px-4 py-3">Zone</th>
                        <th class="px-4 py-3">Method</th>
                        <th class="px-4 py-3">Option</th>
                        <th class="px-4 py-3">Price (GHS)</th>
                        <th class="px-4 py-3">ETA</th>
                        <th class="px-4 py-3">Active</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @foreach ($rules as $rule)
                        <tr class="hover:bg-neutral-50/80">
                            <td class="px-4 py-3 font-medium text-neutral-900">{{ $rule->zone }}</td>
                            <td class="px-4 py-3 text-neutral-700">{{ $rule->method }}</td>
                            <td class="px-4 py-3 text-neutral-700">{{ $rule->option }}</td>
                            <td class="px-4 py-3 tabular-nums text-neutral-700">{{ number_format((float) $rule->price, 2) }}</td>
                            <td class="max-w-xs truncate px-4 py-3 text-neutral-600" title="{{ $rule->estimated_time }}">{{ $rule->estimated_time ?: '—' }}</td>
                            <td class="px-4 py-3">{{ $rule->active ? 'Yes' : 'No' }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('dashboard.delivery-rules.edit', $rule) }}" class="font-medium text-[#0057b8] underline decoration-[#cce0f7] underline-offset-2">Edit</a>
                                <form action="{{ route('dashboard.delivery-rules.destroy', $rule) }}" method="post" class="ml-3 inline" onsubmit="return confirm('Delete this rule?');">
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
        <div class="mt-6">{{ $rules->links() }}</div>
    @endif
@endsection
