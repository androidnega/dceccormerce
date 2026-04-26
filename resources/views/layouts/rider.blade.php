<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Rider — '.config('app.name'))</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'system-ui', 'sans-serif'] },
                },
            },
        };
    </script>
</head>
<body class="min-h-screen min-w-0 overflow-x-hidden bg-slate-50 font-sans text-slate-900 antialiased">
    <div class="flex min-h-screen flex-col md:flex-row">
        <aside class="border-b border-emerald-800 bg-emerald-700 text-white md:sticky md:top-0 md:flex md:h-screen md:w-56 md:shrink-0 md:flex-col md:border-b-0 md:border-r">
            <div class="flex items-center justify-between px-4 py-3 md:block md:border-b md:border-emerald-800 md:px-4 md:py-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-emerald-200">Delivery</p>
                    <p class="truncate text-sm font-bold">{{ config('app.name') }}</p>
                </div>
            </div>
            <nav class="flex gap-1 overflow-x-auto px-2 pb-2 md:flex-col md:overflow-visible md:p-2 md:pb-0">
                <a href="{{ route('rider.dashboard') }}" class="whitespace-nowrap rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('rider.dashboard') ? 'bg-white text-emerald-800' : 'text-white hover:bg-emerald-600' }}">
                    <i class="fa-solid fa-list-check mr-1.5 text-xs opacity-90"></i> My orders
                </a>
                <a href="{{ route('home') }}" class="whitespace-nowrap rounded-lg px-3 py-2 text-sm font-medium text-white hover:bg-emerald-600">
                    <i class="fa-solid fa-store mr-1.5 text-xs"></i> Store
                </a>
            </nav>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="border-b border-slate-200 bg-white px-4 py-3 md:px-8">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <h1 class="text-lg font-semibold text-slate-900">@yield('heading', 'Rider')</h1>
                        @hasSection('subheading')
                            <p class="mt-0.5 text-sm text-slate-600">@yield('subheading')</p>
                        @endif
                    </div>
                    <div class="ml-auto shrink-0">
                        @include('partials.user-profile-menu', ['context' => 'rider'])
                    </div>
                </div>
            </header>

            <main class="flex-1 px-4 py-6 md:px-8 md:py-8">
                @if (session('status'))
                    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                        {{ session('status') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                        <ul class="list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </main>

        </div>
    </div>
    @stack('scripts')
</body>
</html>
