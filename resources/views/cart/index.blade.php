@extends('layout')

@section('title', 'Cart — ' . config('app.name'))
@section('main_class', 'w-full flex-1 bg-gray-50')

@section('content')
    <div class="store-box pb-16 pt-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">Shopping cart</h1>
                <p class="mt-1 text-sm text-gray-500">Review items before checkout. Amounts in {{ config('store.currency_code') }}.</p>
            </div>
            <button type="button" class="store-btn-press rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-800 shadow-sm transition hover:border-gray-300" data-cart-drawer-open>
                Open cart drawer
            </button>
        </div>

        @if (empty($lines))
            <div class="mt-12 rounded-2xl border border-dashed border-gray-200 bg-white px-8 py-16 text-center text-gray-500">
                Your cart is empty.
                <div class="mt-6">
                    <a href="{{ route('products.index') }}" class="font-medium text-gray-900 underline underline-offset-2">Browse products</a>
                </div>
            </div>
        @else
            <div class="mt-10 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Items</h2>
                </div>
                <div class="p-5">
                    @include('cart.partials.drawer-body', ['lines' => $lines, 'total' => $total, 'hideCartPageLink' => true, 'recommendedProducts' => $recommendedProducts])
                </div>
            </div>
        @endif
    </div>
@endsection
