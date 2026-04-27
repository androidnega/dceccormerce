<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard — '.config('app.name'))</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'system-ui', 'sans-serif'] },
                    colors: {
                        admin: {
                            accent: '#ff6000',
                            surface: '#f4f6f9',
                        },
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
                    },
                },
            },
        };
    </script>
</head>
<body class="min-h-screen min-w-0 overflow-x-hidden bg-[#f4f6f9] font-sans text-slate-900 antialiased">
    <style>
        /* Hide scrollbar but keep scrolling (Firefox + WebKit). */
        .admin-no-scrollbar {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .admin-no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        @media (max-width: 767.98px) {
            [data-admin-app-sidebar] {
                transform: translate3d(-100%, 0, 0);
            }
            [data-admin-app-sidebar].admin-sidebar-open {
                transform: translate3d(0, 0, 0);
            }
        }
    </style>
    <div
        id="admin-sidebar-backdrop"
        class="fixed inset-0 z-[45] hidden bg-slate-900/50 md:hidden"
        aria-hidden="true"
        data-admin-sidebar-backdrop
    ></div>
    <div class="md:grid md:min-h-screen md:w-full md:grid-cols-[minmax(0,15rem)_1fr] lg:grid-cols-[minmax(0,16rem)_1fr]">
        <aside
            id="admin-sidebar-panel"
            data-admin-app-sidebar
            class="flex flex-col border-b border-slate-200 bg-slate-50 text-slate-800 max-md:fixed max-md:inset-y-0 max-md:left-0 max-md:z-50 max-md:h-[100dvh] max-md:w-[min(17.5rem,88vw)] max-md:overflow-y-auto max-md:border-r max-md:shadow-2xl max-md:transition-transform max-md:duration-200 max-md:ease-out md:sticky md:top-0 md:h-screen md:border-b-0 md:border-r md:border-slate-200 md:shadow-none"
        >
            <div class="flex items-center gap-3 border-b border-slate-200 bg-white px-4 py-4">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-orange-50 text-[#ff6000] ring-1 ring-orange-100">
                    <i class="fa-solid fa-grip text-sm" aria-hidden="true"></i>
                </span>
                <div class="min-w-0 flex-1 leading-tight">
                    <p class="truncate text-sm font-semibold text-slate-900">{{ config('app.name') }}</p>
                    <p class="text-xs text-slate-500">{{ auth()->user()->role === 'admin' ? 'Administrator' : 'Store manager' }}</p>
                </div>
                <button
                    type="button"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 md:hidden"
                    aria-label="Close menu"
                    data-admin-sidebar-close
                >
                    <i class="fa-solid fa-xmark text-lg" aria-hidden="true"></i>
                </button>
            </div>

            @include('partials.dashboard-sidebar')
        </aside>

        <div class="flex min-h-screen min-w-0 flex-col bg-[#f4f6f9] md:h-screen md:overflow-y-auto">
            <header class="flex flex-wrap items-center gap-3 border-b border-slate-200/90 bg-slate-800 px-4 py-3 shadow-sm lg:gap-4 lg:px-6">
                <button
                    type="button"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-slate-600/80 bg-slate-700/80 text-white transition hover:bg-slate-700 md:hidden"
                    aria-expanded="false"
                    aria-controls="admin-sidebar-panel"
                    data-admin-sidebar-open
                >
                    <i class="fa-solid fa-bars text-lg" aria-hidden="true"></i>
                </button>
                <div class="min-w-0 flex-1 basis-full sm:basis-auto">
                    <h1 class="text-lg font-semibold tracking-tight text-white">@yield('heading', 'Dashboard')</h1>
                    @hasSection('subheading')
                        <p class="mt-0.5 text-sm text-slate-400">@yield('subheading')</p>
                    @endif
                </div>
                <div class="hidden min-w-0 flex-1 md:block md:max-w-md lg:max-w-lg xl:max-w-xl">
                    <label class="sr-only" for="admin-dashboard-search">Search</label>
                    <div class="relative w-full">
                        <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[13px] text-slate-400" aria-hidden="true"></i>
                        <input
                            id="admin-dashboard-search"
                            type="search"
                            placeholder="Search…"
                            class="w-full rounded-lg border border-slate-600/80 bg-slate-700/50 py-2 pl-9 pr-3 text-sm text-white placeholder:text-slate-400 focus:border-orange-400/50 focus:outline-none focus:ring-2 focus:ring-orange-500/25"
                            autocomplete="off"
                        >
                    </div>
                </div>
                <div class="ml-auto shrink-0">
                    @include('partials.user-profile-menu', ['context' => 'dashboard'])
                </div>
            </header>

            <main class="flex-1 px-4 py-6 lg:px-8 lg:py-8">
                @if (session('status'))
                    <div class="mb-6 rounded-xl border border-emerald-200/80 bg-emerald-50/90 px-4 py-3 text-sm text-emerald-900 shadow-sm">
                        {{ session('status') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-6 rounded-lg border border-[#ffe37a] bg-[#fffbe6] px-4 py-3 text-sm text-[#8a6a00]">
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
    <script>
        (function () {
            var aside = document.querySelector('[data-admin-app-sidebar]');
            if (!aside) return;

            var nav = aside.querySelector('nav[data-admin-sidebar="1"]') || aside.querySelector('nav');
            if (!nav) return;

            function bindGroup(group) {
                var btn = nav.querySelector('[data-sidebar-group-toggle="' + group + '"]');
                var items = nav.querySelector('[data-sidebar-group-items="' + group + '"]');
                if (!btn || !items) return;

                btn.addEventListener('click', function () {
                    var willOpen = items.classList.contains('hidden');
                    items.classList.toggle('hidden', !willOpen);
                    btn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');

                    var chevron = btn.querySelector('[data-sidebar-group-chevron="' + group + '"]');
                    if (chevron) chevron.classList.toggle('rotate-90', willOpen);
                });
            }

            ['logistics', 'catalog', 'homepage', 'system'].forEach(bindGroup);

            var backdrop = document.querySelector('[data-admin-sidebar-backdrop]');
            var openBtn = document.querySelector('[data-admin-sidebar-open]');
            var closeEls = document.querySelectorAll('[data-admin-sidebar-close]');

            function isMobileNav() {
                return window.matchMedia('(max-width: 767.98px)').matches;
            }

            function setMobileOpen(open) {
                if (!isMobileNav()) {
                    aside.classList.remove('admin-sidebar-open');
                    if (backdrop) {
                        backdrop.classList.add('hidden');
                        backdrop.setAttribute('aria-hidden', 'true');
                    }
                    document.body.classList.remove('overflow-hidden');
                    if (openBtn) openBtn.setAttribute('aria-expanded', 'false');
                    return;
                }
                aside.classList.toggle('admin-sidebar-open', open);
                if (backdrop) {
                    backdrop.classList.toggle('hidden', !open);
                    backdrop.setAttribute('aria-hidden', open ? 'false' : 'true');
                }
                document.body.classList.toggle('overflow-hidden', open);
                if (openBtn) openBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
            }

            if (openBtn) {
                openBtn.addEventListener('click', function () {
                    setMobileOpen(!aside.classList.contains('admin-sidebar-open'));
                });
            }
            closeEls.forEach(function (el) {
                el.addEventListener('click', function () {
                    setMobileOpen(false);
                });
            });
            if (backdrop) {
                backdrop.addEventListener('click', function () {
                    setMobileOpen(false);
                });
            }
            aside.querySelectorAll('a[href]').forEach(function (link) {
                link.addEventListener('click', function () {
                    if (isMobileNav()) setMobileOpen(false);
                });
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && aside.classList.contains('admin-sidebar-open')) {
                    setMobileOpen(false);
                }
            });
            window.addEventListener('resize', function () {
                if (!isMobileNav()) setMobileOpen(false);
            });
        })();
    </script>
</body>
</html>
