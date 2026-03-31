@extends('layout')

@section('title', 'Account — ' . config('app.name'))

@section('content')
    <div class="mx-auto max-w-2xl">
        <p class="text-xs font-medium uppercase tracking-widest text-neutral-500">Apple products &amp; accessories</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-neutral-900">Your account</h1>
        <p class="mt-2 text-neutral-500">Welcome back, <span class="font-medium text-neutral-900">{{ auth()->user()->name }}</span>.</p>
        <div class="mt-10 flex flex-wrap gap-3">
            <a href="{{ route('account.orders.index') }}" class="rounded-full bg-primary-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-primary-700">My orders</a>
            <a href="{{ route('home') }}" class="rounded-full border border-neutral-200 bg-white px-6 py-2.5 text-sm font-medium text-neutral-900 shadow-sm hover:bg-neutral-50">Shop</a>
        </div>
    </div>
@endsection
