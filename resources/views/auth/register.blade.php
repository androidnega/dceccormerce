@extends('layout')

@section('title', 'Register — ' . config('app.name'))

@section('content')
    <div class="mx-auto w-full max-w-md min-w-0">
        <h1 class="text-2xl font-bold text-slate-900">Create account</h1>
        <p class="mt-1 text-sm text-slate-600">Customer registration only. Admin accounts are created separately.</p>

        <form method="post" action="{{ route('register') }}" class="mt-8 space-y-5">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required autocomplete="name"
                    class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email"
                    class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500 @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                <input type="password" name="password" id="password" required autocomplete="new-password"
                    class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500 @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirm password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                    class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
            </div>
            <button type="submit" class="w-full rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                Register
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-600">
            Already have an account?
            <a href="{{ route('login') }}" class="font-medium text-slate-900 underline">Login</a>
        </p>
    </div>
@endsection
