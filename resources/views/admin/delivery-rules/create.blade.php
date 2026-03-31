@extends('layouts.dashboard')

@section('title', 'New delivery rule — Admin')
@section('heading', 'New delivery rule')

@section('content')
    <form action="{{ route('dashboard.delivery-rules.store') }}" method="post" class="mt-6 max-w-xl space-y-5">
        @csrf
        <div>
            <label for="zone" class="block text-sm font-medium text-slate-700">Zone <span class="text-red-600">*</span></label>
            <input type="text" name="zone" id="zone" value="{{ old('zone') }}" required placeholder="e.g. Accra" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
            @error('zone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="method" class="block text-sm font-medium text-slate-700">Fulfillment method <span class="text-red-600">*</span></label>
            <select name="method" id="method" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                @foreach (['rider', 'driver', 'third_party', 'pickup', 'manual'] as $m)
                    <option value="{{ $m }}" @selected(old('method') === $m)>{{ $m }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="option" class="block text-sm font-medium text-slate-700">Customer option <span class="text-red-600">*</span></label>
            <select name="option" id="option" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                @foreach (['standard', 'express', 'pickup'] as $o)
                    <option value="{{ $o }}" @selected(old('option', 'standard') === $o)>{{ $o }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="price" class="block text-sm font-medium text-slate-700">Price (GHS) <span class="text-red-600">*</span></label>
            <input type="number" name="price" id="price" value="{{ old('price', '0') }}" step="0.01" min="0" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
        </div>
        <div>
            <label for="estimated_time" class="block text-sm font-medium text-slate-700">Estimated time</label>
            <input type="text" name="estimated_time" id="estimated_time" value="{{ old('estimated_time') }}" placeholder="e.g. 2–4 hours" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
        </div>
        <div class="flex items-center gap-2">
            <input type="hidden" name="active" value="0">
            <input type="checkbox" name="active" id="active" value="1" class="rounded border-slate-300 text-slate-900 focus:ring-slate-500" @checked(old('active', true))>
            <label for="active" class="text-sm text-slate-700">Active</label>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="rounded-full bg-[#0057b8] px-5 py-2.5 text-sm font-medium text-white hover:bg-[#00479a]">Save</button>
            <a href="{{ route('dashboard.delivery-rules.index') }}" class="rounded-full border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
        </div>
    </form>
@endsection
