@extends('layouts.dashboard')

@section('title', 'Edit delivery agent — Admin')
@section('heading', 'Edit delivery agent')

@section('content')
    <form action="{{ route('dashboard.delivery-agents.update', $agent) }}" method="post" class="mt-6 max-w-xl space-y-5">
        @csrf
        @method('PUT')
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700">Name <span class="text-red-600">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name', $agent->name) }}" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
        </div>
        <div>
            <label for="type" class="block text-sm font-medium text-slate-700">Type <span class="text-red-600">*</span></label>
            <select name="type" id="type" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                @foreach (['rider', 'driver', 'third_party', 'pickup', 'manual'] as $t)
                    <option value="{{ $t }}" @selected(old('type', $agent->type) === $t)>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="phone" class="block text-sm font-medium text-slate-700">Phone</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', $agent->phone) }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
        </div>
        <div>
            <label for="vehicle_type" class="block text-sm font-medium text-slate-700">Vehicle</label>
            <input type="text" name="vehicle_type" id="vehicle_type" value="{{ old('vehicle_type', $agent->vehicle_type) }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-slate-700">Status <span class="text-red-600">*</span></label>
            <select name="status" id="status" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                @foreach (['available', 'busy', 'offline'] as $s)
                    <option value="{{ $s }}" @selected(old('status', $agent->status) === $s)>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="rider_id" class="block text-sm font-medium text-slate-700">Link to rider account</label>
            <select name="rider_id" id="rider_id" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                <option value="">— None —</option>
                @foreach ($riders as $rider)
                    <option value="{{ $rider->id }}" @selected((string) old('rider_id', $agent->rider_id) === (string) $rider->id)>{{ $rider->name }} ({{ $rider->phone }})</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="rounded-full bg-[#0057b8] px-5 py-2.5 text-sm font-medium text-white hover:bg-[#00479a]">Update</button>
            <a href="{{ route('dashboard.delivery-agents.index') }}" class="rounded-full border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
        </div>
    </form>
@endsection
