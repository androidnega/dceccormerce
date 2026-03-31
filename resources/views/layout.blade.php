<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'system-ui', 'sans-serif'] },
                    animation: {
                        'fade-up': 'fadeUp 0.55s cubic-bezier(0.22, 1, 0.36, 1) forwards',
                    },
                    keyframes: {
                        fadeUp: {
                            '0%': { opacity: '0', transform: 'translateY(14px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                    },
                    colors: {
                        primary: {
                            50: '#e6f0fb',
                            100: '#cce0f7',
                            200: '#99c2ef',
                            300: '#66a3e6',
                            400: '#3385de',
                            500: '#0057b8',
                            600: '#00479a',
                            700: '#00377b',
                            800: '#00275c',
                            900: '#00183e',
                            950: '#000c1f',
                        },
                        store: {
                            dark: '#0057b8',
                            medium: '#0057b8',
                            yellow: '#ffd700',
                            accent: '#0057b8',
                            footer: '#0057b8',
                        },
                    },
                },
            },
        };
    </script>
    @include('partials.store-styles')
</head>
<body class="store-theme flex min-h-screen flex-col bg-white font-sans text-slate-900 antialiased">
    @include('partials.store-header')

    <main class="@yield('main_class', 'mx-auto w-full max-w-6xl flex-1 px-4 py-10')">
        @if (session('status'))
            <div class="mx-auto mb-6 max-w-6xl px-4 sm:px-6">
                <div class="rounded-2xl border border-primary-200 bg-white px-4 py-3 text-sm text-primary-900">
                    {{ session('status') }}
                </div>
            </div>
        @endif
        @if ($errors->any())
            <div class="mx-auto mb-6 max-w-6xl px-4 sm:px-6">
            <div class="rounded-2xl border border-primary-200 bg-[#fff8cc] px-4 py-3 text-sm text-primary-900">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            </div>
        @endif
        @yield('content')
    </main>

    @include('partials.store-footer')

    @include('partials.store-cart-drawer')
    @include('products.partials.product-quick-view-modal')
    @include('partials.store-storefront-scripts')

    <button type="button" id="store-scroll-top" class="fixed bottom-6 right-5 z-[60] flex h-11 w-11 items-center justify-center rounded bg-[#0057b8] text-white shadow-lg transition hover:bg-[#00479a] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0057b8] focus-visible:ring-offset-2 md:bottom-8 md:right-8" aria-label="Back to top" hidden>
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"/></svg>
    </button>
    <script>
        (function () {
            document.querySelectorAll('[data-store-nav-toggle]').forEach(function (btn) {
                var panel = document.querySelector('[data-store-mobile-panel]');
                if (!panel) return;
                btn.addEventListener('click', function () {
                    var open = !panel.classList.contains('hidden');
                    panel.classList.toggle('hidden', open);
                    btn.setAttribute('aria-expanded', open ? 'false' : 'true');
                });
            });
            if ('IntersectionObserver' in window) {
                var io = new IntersectionObserver(function (entries) {
                    entries.forEach(function (e) {
                        if (e.isIntersecting) e.target.classList.add('store-reveal-visible');
                    });
                }, { threshold: 0.06, rootMargin: '0px 0px -40px 0px' });
                document.querySelectorAll('.store-reveal').forEach(function (el) { io.observe(el); });
            } else {
                document.querySelectorAll('.store-reveal').forEach(function (el) { el.classList.add('store-reveal-visible'); });
            }
            var topBtn = document.getElementById('store-scroll-top');
            if (topBtn) {
                var toggleTop = function () {
                    topBtn.hidden = window.scrollY < 320;
                };
                toggleTop();
                window.addEventListener('scroll', toggleTop, { passive: true });
                topBtn.addEventListener('click', function () {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            }
        })();
    </script>
    @stack('scripts')
</body>
</html>
