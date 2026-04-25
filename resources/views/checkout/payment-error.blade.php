@extends('layout')

@section('title', 'Payment — ' . config('app.name'))

@section('content')
    <div class="max-w-xl rounded-lg border border-amber-200 bg-amber-50 px-4 py-4 text-amber-950">
        <p class="font-semibold">We could not match this payment to checkout</p>
        <p class="mt-2 text-sm leading-relaxed">
            If money left your account, contact support with Paystack reference
            <span class="font-mono font-medium">{{ $reference }}</span>.
        </p>
        <p class="mt-4 text-sm">
            <a href="{{ route('checkout.index') }}" class="font-medium underline">Return to checkout</a>
        </p>
    </div>
@endsection
