<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $product->name }} — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen min-w-0 overflow-x-hidden bg-neutral-100 antialiased">
    <div class="mx-auto w-full min-w-0 max-w-3xl px-4 py-8 sm:px-6 sm:py-10">
        <p class="mb-6 text-center">
            <a href="{{ route('products.show', $product) }}" class="text-sm font-medium text-primary-800 underline decoration-primary-300 underline-offset-2 hover:text-primary-950">← Back to product</a>
        </p>
        <div class="mx-auto w-full max-w-2xl overflow-hidden rounded-lg bg-white p-3 shadow-md ring-1 ring-neutral-200/90 sm:p-4">
            <img
                src="{{ $image->url() }}"
                alt="{{ $product->name }}"
                class="mx-auto h-auto max-h-[min(85vh,900px)] w-full max-w-full object-contain"
            >
        </div>
    </div>
</body>
</html>
