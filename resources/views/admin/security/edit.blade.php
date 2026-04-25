@extends('layouts.dashboard')

@section('title', 'Password — '.config('app.name'))
@section('heading', 'Account security')
@section('subheading', 'Change the password for your staff account.')

@section('content')
    <div class="max-w-lg rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="post" action="{{ route('dashboard.security.update') }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="current_password" class="block text-sm font-medium text-slate-700">Current password</label>
                <input type="password" name="current_password" id="current_password" required autocomplete="current-password" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-[#ff6000] focus:outline-none focus:ring-1 focus:ring-[#ff6000] @error('current_password') border-red-500 @enderror">
                @error('current_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">New password</label>
                <input type="password" name="password" id="password" required autocomplete="new-password" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-[#ff6000] focus:outline-none focus:ring-1 focus:ring-[#ff6000] @error('password') border-red-500 @enderror">
                <p class="mt-1 text-xs text-slate-500">At least 8 characters.</p>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirm new password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-[#ff6000] focus:outline-none focus:ring-1 focus:ring-[#ff6000]">
            </div>

            <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                Update password
            </button>
        </form>
    </div>
@endsection
