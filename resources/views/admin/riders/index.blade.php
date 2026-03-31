@extends('layouts.dashboard')

@section('title', 'Riders — Admin')
@section('heading', 'Riders')
@section('subheading', 'Manage delivery riders and login accounts.')

@section('content')
    <div>
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-neutral-600">Riders on delivery are marked unavailable until the order is delivered or failed.</p>
            <a href="{{ route('dashboard.riders.create') }}" class="inline-flex shrink-0 rounded-full bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">Add rider</a>
        </div>

        @if ($riders->isEmpty())
            <div class="rounded-2xl border border-dashed border-neutral-200 bg-neutral-50/50 px-6 py-16 text-center text-sm text-neutral-500">
                No riders yet. Add a rider with email and password so they can log in.
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($riders as $rider)
                    <article class="flex flex-col rounded-2xl border border-neutral-100 bg-white p-5 shadow-sm transition hover:border-neutral-200 hover:shadow-md">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h2 class="truncate text-base font-semibold text-neutral-900">{{ $rider->name }}</h2>
                                @if ($rider->user)
                                    <p class="mt-1 truncate text-xs text-neutral-500">{{ $rider->user->email }}</p>
                                @else
                                    <p class="mt-1 text-xs text-amber-700">No login linked</p>
                                @endif
                                <p class="mt-1 font-mono text-sm text-neutral-600">{{ $rider->phone }}</p>
                            </div>
                            <span class="shrink-0 rounded-full px-2.5 py-0.5 text-[11px] font-semibold uppercase tracking-wide {{ $rider->is_available ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                {{ $rider->is_available ? 'Available' : 'Busy' }}
                            </span>
                        </div>
                        <div class="mt-4 flex items-center gap-2 border-t border-neutral-100 pt-4 text-sm text-neutral-600">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-neutral-50 text-neutral-500">
                                <i class="fa-solid fa-motorcycle text-xs" aria-hidden="true"></i>
                            </span>
                            <span>{{ ucfirst($rider->vehicle_type) }}</span>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $riders->links() }}
            </div>
        @endif
    </div>
@endsection
