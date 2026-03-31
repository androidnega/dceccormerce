@extends('layouts.dashboard')

@section('title', 'Add Rider — Admin')
@section('heading', 'Add Rider')
@section('subheading', 'Create a login (email + password) and delivery profile.')

@section('content')
    <div class="max-w-2xl rounded-2xl border border-neutral-100 bg-white p-8 shadow-sm">
        <form action="{{ route('dashboard.riders.store') }}" method="post" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-neutral-700">Full name <span class="text-red-600">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required class="mt-1 w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm text-neutral-900 focus:border-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-400">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-neutral-700">Email (login) <span class="text-red-600">*</span></label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="mt-1 w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm text-neutral-900 focus:border-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-400">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-neutral-700">Password <span class="text-red-600">*</span></label>
                <input type="password" id="password" name="password" required autocomplete="new-password" class="mt-1 w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm text-neutral-900 focus:border-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-400">
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-neutral-700">Confirm password <span class="text-red-600">*</span></label>
                <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" class="mt-1 w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm text-neutral-900 focus:border-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-400">
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium text-neutral-700">Phone (delivery contact) <span class="text-red-600">*</span></label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required class="mt-1 w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm text-neutral-900 focus:border-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-400">
            </div>
            <div>
                <label for="vehicle_type" class="block text-sm font-medium text-neutral-700">Vehicle type</label>
                <select id="vehicle_type" name="vehicle_type" class="mt-1 w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm text-neutral-900 focus:border-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-400" required>
                    <option value="bike" @selected(old('vehicle_type', 'bike') === 'bike')>Bike</option>
                    <option value="car" @selected(old('vehicle_type') === 'car')>Car</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" id="is_available" name="is_available" value="1" @checked(old('is_available', '1') === '1')>
                <label for="is_available" class="text-sm text-neutral-700">Available for assignment</label>
            </div>
            <div class="pt-2">
                <button type="submit" class="rounded-full bg-primary-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-700">Create rider account</button>
            </div>
        </form>
    </div>
@endsection
