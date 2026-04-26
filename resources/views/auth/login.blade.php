@extends('layout')

@section('title', 'Login — ' . config('app.name'))

@section('content')
    <div class="mx-auto w-full max-w-md min-w-0">
        <h1 class="text-2xl font-bold text-slate-900">Login</h1>
        <p class="mt-1 text-sm text-slate-600">Sign in to your account.</p>

        <form method="post" action="{{ route('login') }}" class="mt-8 space-y-5">
            @csrf
            <div>
                <label for="login" class="block text-sm font-medium text-slate-700">Email or username</label>
                <input type="text" name="login" id="login" value="{{ old('login') }}" required autocomplete="username"
                    class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500 @error('login') border-red-500 @enderror">
                @error('login')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                <input type="password" name="password" id="password" required autocomplete="current-password"
                    class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500 @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="remember" id="remember" class="rounded border-slate-300 text-slate-900 focus:ring-slate-500">
                <label for="remember" class="text-sm text-slate-700">Remember me</label>
            </div>
            <button type="submit" class="w-full rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                Login
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-600">
            No account?
            <a href="{{ route('register') }}" class="font-medium text-slate-900 underline">Register</a>
        </p>
    </div>
@endsection
