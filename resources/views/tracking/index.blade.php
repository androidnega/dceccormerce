@extends('layout')

@section('title', 'Track order — ' . config('app.name'))

@section('content')
    <div class="mx-auto max-w-lg px-4 sm:px-0">
        <h1 class="text-2xl font-bold tracking-tight text-neutral-900 sm:text-3xl">Track your order</h1>
        <p class="mt-2 text-sm text-neutral-600">Enter the order number from your confirmation (for example <span class="font-mono text-neutral-800">DCA-2026-0001</span>).</p>

        <form action="{{ route('tracking.lookup') }}" method="post" class="mt-8 rounded-2xl border border-neutral-100 bg-white p-6 shadow-sm">
            @csrf
            <label for="order_number" class="block text-sm font-medium text-neutral-800">Order number</label>
            <input
                type="text"
                name="order_number"
                id="order_number"
                value="{{ old('order_number') }}"
                required
                autocomplete="off"
                inputmode="text"
                placeholder="DCA-2026-0001"
                class="mt-2 w-full rounded-xl border border-neutral-200 px-4 py-3 text-base font-mono text-neutral-900 placeholder:text-neutral-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
            >
            @error('order_number')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
            <label for="token" class="mt-4 block text-sm font-medium text-neutral-800">Access token</label>
            <p class="mt-1 text-xs text-neutral-500">From your confirmation email or success page.</p>
            <input
                type="text"
                name="token"
                id="token"
                value="{{ old('token') }}"
                required
                autocomplete="off"
                class="mt-2 w-full rounded-xl border border-neutral-200 px-4 py-3 text-sm font-mono text-neutral-900 placeholder:text-neutral-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
                placeholder="Your private token"
            >
            @error('token')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
            <button type="submit" class="mt-4 w-full rounded-full bg-primary-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-primary-700 active:scale-[0.99]">
                Track delivery
            </button>
        </form>
    </div>
@endsection
