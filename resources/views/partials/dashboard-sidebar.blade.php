@php
    $isAdmin = auth()->user()->role === 'admin';
    $isManager = auth()->user()->role === 'manager';
    $sidebarLogisticsOpen = request()->routeIs('dashboard.delivery-rules.*');
    $sidebarCatalogOpen = request()->routeIs('dashboard.categories.*') || request()->routeIs('dashboard.products.*');
    $sidebarHomepageOpen = request()->routeIs('dashboard.hero-slides.*')
        || request()->routeIs('dashboard.homepage-settings.*')
        || request()->routeIs('dashboard.homepage-sections.*')
        || request()->routeIs('dashboard.category-banners.*')
        || request()->routeIs('dashboard.sale-spotlight.*')
        || request()->routeIs('dashboard.news-posts.*')
        || request()->routeIs('dashboard.promos.*')
        || request()->routeIs('dashboard.store-product-display.*');
    $sidebarSystemOpen = request()->routeIs('dashboard.integrations.*');
@endphp

<nav data-admin-sidebar="1" class="admin-no-scrollbar flex flex-1 flex-col gap-0.5 overflow-y-auto bg-slate-50 p-2 text-sm font-medium">
    <a href="{{ route('dashboard.index') }}" class="flex items-center gap-2.5 rounded-md px-3 py-2 transition {{ request()->routeIs('dashboard.index') ? 'bg-orange-50 font-medium text-orange-700 ring-1 ring-orange-100' : 'text-slate-600 hover:bg-white hover:text-slate-900' }}">
        <i class="fa-solid fa-chart-pie w-4 text-center text-xs text-inherit opacity-90" aria-hidden="true"></i>
        Overview
    </a>

    @if ($isManager)
        <a href="{{ route('dashboard.orders.index') }}" class="flex items-center gap-2.5 rounded-md px-3 py-2 transition {{ request()->routeIs('dashboard.orders.*') ? 'bg-orange-50 font-medium text-orange-700 ring-1 ring-orange-100' : 'text-slate-600 hover:bg-white hover:text-slate-900' }}">
            <i class="fa-solid fa-receipt w-4 text-center text-xs text-inherit opacity-90" aria-hidden="true"></i>
            Orders
        </a>
        <a href="{{ route('dashboard.riders.index') }}" class="flex items-center gap-2.5 rounded-md px-3 py-2 transition {{ request()->routeIs('dashboard.riders.*') ? 'bg-orange-50 font-medium text-orange-700 ring-1 ring-orange-100' : 'text-slate-600 hover:bg-white hover:text-slate-900' }}">
            <i class="fa-solid fa-motorcycle w-4 text-center text-xs text-inherit opacity-90" aria-hidden="true"></i>
            Riders
        </a>
        <a href="{{ route('dashboard.delivery-agents.index') }}" class="flex items-center gap-2.5 rounded-md px-3 py-2 transition {{ request()->routeIs('dashboard.delivery-agents.*') ? 'bg-orange-50 font-medium text-orange-700 ring-1 ring-orange-100' : 'text-slate-600 hover:bg-white hover:text-slate-900' }}">
            <i class="fa-solid fa-user-gear w-4 text-center text-xs text-inherit opacity-90" aria-hidden="true"></i>
            Delivery agents
        </a>
    @endif

    @if ($isAdmin)
        <button
            type="button"
            data-sidebar-group-toggle="logistics"
            aria-expanded="{{ $sidebarLogisticsOpen ? 'true' : 'false' }}"
            class="mt-2 flex w-full items-center justify-between rounded-md px-3 py-2 text-left text-[0.92rem] font-medium text-slate-700 transition hover:bg-slate-100"
        >
            <span class="flex items-center gap-2">
                <i class="fa-solid fa-truck-fast w-4 text-center text-xs text-inherit opacity-90" aria-hidden="true"></i>
                Logistics
            </span>
            <span
                data-sidebar-group-chevron="logistics"
                class="{{ $sidebarLogisticsOpen ? 'rotate-90' : '' }} inline-flex h-6 w-6 items-center justify-center rounded-full bg-slate-200/90 text-[11px] text-slate-600 transition"
                aria-hidden="true"
            >
                &gt;
            </span>
        </button>
        <div
            data-sidebar-group-items="logistics"
            class="flex flex-col gap-0.5 {{ $sidebarLogisticsOpen ? '' : 'hidden' }}"
        >
            <a href="{{ route('dashboard.delivery-rules.index') }}" class="flex items-center gap-2.5 rounded-md px-3 py-2 transition {{ request()->routeIs('dashboard.delivery-rules.*') ? 'bg-orange-50 font-medium text-orange-700 ring-1 ring-orange-100' : 'text-slate-600 hover:bg-white hover:text-slate-900' }}">
                <i class="fa-solid fa-route w-4 text-center text-xs text-inherit opacity-90" aria-hidden="true"></i>
                Delivery rules
            </a>
        </div>

        <button
            type="button"
            data-sidebar-group-toggle="catalog"
            aria-expanded="{{ $sidebarCatalogOpen ? 'true' : 'false' }}"
            class="mt-2 flex w-full items-center justify-between rounded-md px-3 py-2 text-left text-[0.92rem] font-medium text-slate-700 transition hover:bg-slate-100"
        >
            <span class="flex items-center gap-2">
                <i class="fa-solid fa-box-open w-4 text-center text-xs text-inherit opacity-90" aria-hidden="true"></i>
                Catalog
            </span>
            <span
                data-sidebar-group-chevron="catalog"
                class="{{ $sidebarCatalogOpen ? 'rotate-90' : '' }} inline-flex h-6 w-6 items-center justify-center rounded-full bg-slate-200/90 text-[11px] text-slate-600 transition"
                aria-hidden="true"
            >
                &gt;
            </span>
        </button>
        <div
            data-sidebar-group-items="catalog"
            class="flex flex-col gap-0.5 {{ $sidebarCatalogOpen ? '' : 'hidden' }}"
        >
            <a href="{{ route('dashboard.categories.index') }}" class="flex items-center gap-2.5 rounded-md px-3 py-2 transition {{ request()->routeIs('dashboard.categories.*') ? 'bg-orange-50 font-medium text-orange-700 ring-1 ring-orange-100' : 'text-slate-600 hover:bg-white hover:text-slate-900' }}">
                <i class="fa-solid fa-folder-tree w-4 text-center text-xs text-inherit opacity-90" aria-hidden="true"></i>
                Categories
            </a>
            <a href="{{ route('dashboard.products.index') }}" class="flex items-center gap-2.5 rounded-md px-3 py-2 transition {{ request()->routeIs('dashboard.products.*') ? 'bg-orange-50 font-medium text-orange-700 ring-1 ring-orange-100' : 'text-slate-600 hover:bg-white hover:text-slate-900' }}">
                <i class="fa-solid fa-box w-4 text-center text-xs text-inherit opacity-90" aria-hidden="true"></i>
                Products
            </a>
        </div>

        <button
            type="button"
            data-sidebar-group-toggle="homepage"
            aria-expanded="{{ $sidebarHomepageOpen ? 'true' : 'false' }}"
            class="mt-4 flex w-full items-center justify-between rounded-md px-3 py-2 text-left text-[0.92rem] font-medium text-slate-700 transition hover:bg-slate-100"
        >
            <span class="flex items-center gap-2">
                <i class="fa-solid fa-house-chimney w-4 text-center text-xs text-inherit opacity-90" aria-hidden="true"></i>
                Homepage
            </span>
            <span
                data-sidebar-group-chevron="homepage"
                class="{{ $sidebarHomepageOpen ? 'rotate-90' : '' }} inline-flex h-6 w-6 items-center justify-center rounded-full bg-slate-200/90 text-[11px] text-slate-600 transition"
                aria-hidden="true"
            >
                &gt;
            </span>
        </button>
        <div
            data-sidebar-group-items="homepage"
            class="flex flex-col gap-0.5 {{ $sidebarHomepageOpen ? '' : 'hidden' }}"
        >
            <a href="{{ route('dashboard.hero-slides.index') }}" class="flex items-center gap-2.5 rounded-md px-3 py-2 transition {{ request()->routeIs('dashboard.hero-slides.*') ? 'bg-orange-50 font-medium text-orange-700 ring-1 ring-orange-100' : 'text-slate-600 hover:bg-white hover:text-slate-900' }}">
                <i class="fa-solid fa-images w-4 text-center text-xs text-inherit opacity-90" aria-hidden="true"></i>
                Hero
            </a>
            <a href="{{ route('dashboard.homepage-settings.edit') }}" class="flex items-center gap-2.5 rounded-md px-3 py-2 transition {{ request()->routeIs('dashboard.homepage-settings.*') ? 'bg-orange-50 font-medium text-orange-700 ring-1 ring-orange-100' : 'text-slate-600 hover:bg-white hover:text-slate-900' }}">
                <i class="fa-solid fa-house-chimney w-4 text-center text-xs text-inherit opacity-90" aria-hidden="true"></i>
                Homepage
            </a>
            <a href="{{ route('dashboard.category-banners.index') }}" class="flex items-center gap-2.5 rounded-md px-3 py-2 transition {{ request()->routeIs('dashboard.category-banners.*') ? 'bg-orange-50 font-medium text-orange-700 ring-1 ring-orange-100' : 'text-slate-600 hover:bg-white hover:text-slate-900' }}">
                <i class="fa-solid fa-border-all w-4 text-center text-xs text-inherit opacity-90" aria-hidden="true"></i>
                Shop by category
            </a>
            <a href="{{ route('dashboard.homepage-sections.index') }}" class="flex items-center gap-2.5 rounded-md px-3 py-2 transition {{ request()->routeIs('dashboard.homepage-sections.*') ? 'bg-orange-50 font-medium text-orange-700 ring-1 ring-orange-100' : 'text-slate-600 hover:bg-white hover:text-slate-900' }}">
                <i class="fa-solid fa-layer-group w-4 text-center text-xs text-inherit opacity-90" aria-hidden="true"></i>
                Home sections
            </a>
            <a href="{{ route('dashboard.sale-spotlight.edit') }}" class="flex items-center gap-2.5 rounded-md px-3 py-2 transition {{ request()->routeIs('dashboard.sale-spotlight.*') ? 'bg-orange-50 font-medium text-orange-700 ring-1 ring-orange-100' : 'text-slate-600 hover:bg-white hover:text-slate-900' }}">
                <i class="fa-solid fa-bolt w-4 text-center text-xs text-inherit opacity-90" aria-hidden="true"></i>
                On sale spotlight
            </a>
            <a href="{{ route('dashboard.news-posts.index') }}" class="flex items-center gap-2.5 rounded-md px-3 py-2 transition {{ request()->routeIs('dashboard.news-posts.*') ? 'bg-orange-50 font-medium text-orange-700 ring-1 ring-orange-100' : 'text-slate-600 hover:bg-white hover:text-slate-900' }}">
                <i class="fa-solid fa-newspaper w-4 text-center text-xs text-inherit opacity-90" aria-hidden="true"></i>
                Popular news
            </a>
            <a href="{{ route('dashboard.promos.index') }}" class="flex items-center gap-2.5 rounded-md px-3 py-2 transition {{ request()->routeIs('dashboard.promos.*') ? 'bg-orange-50 font-medium text-orange-700 ring-1 ring-orange-100' : 'text-slate-600 hover:bg-white hover:text-slate-900' }}">
                <i class="fa-solid fa-bullhorn w-4 text-center text-xs text-inherit opacity-90" aria-hidden="true"></i>
                Promos
            </a>
            <a href="{{ route('dashboard.store-product-display.edit') }}" class="flex items-center gap-2.5 rounded-md px-3 py-2 transition {{ request()->routeIs('dashboard.store-product-display.*') ? 'bg-orange-50 font-medium text-orange-700 ring-1 ring-orange-100' : 'text-slate-600 hover:bg-white hover:text-slate-900' }}">
                <i class="fa-solid fa-table-cells-large w-4 text-center text-xs text-inherit opacity-90" aria-hidden="true"></i>
                Product display
            </a>
        </div>

        <button
            type="button"
            data-sidebar-group-toggle="system"
            aria-expanded="{{ $sidebarSystemOpen ? 'true' : 'false' }}"
            class="mt-4 flex w-full items-center justify-between rounded-md px-3 py-2 text-left text-[0.92rem] font-medium text-slate-700 transition hover:bg-slate-100"
        >
            <span class="flex items-center gap-2">
                <i class="fa-solid fa-gears w-4 text-center text-xs text-inherit opacity-90" aria-hidden="true"></i>
                System
            </span>
            <span
                data-sidebar-group-chevron="system"
                class="{{ $sidebarSystemOpen ? 'rotate-90' : '' }} inline-flex h-6 w-6 items-center justify-center rounded-full bg-slate-200/90 text-[11px] text-slate-600 transition"
                aria-hidden="true"
            >
                &gt;
            </span>
        </button>
        <div
            data-sidebar-group-items="system"
            class="flex flex-col gap-0.5 {{ $sidebarSystemOpen ? '' : 'hidden' }}"
        >
            <a href="{{ route('dashboard.integrations.edit') }}" class="flex items-center gap-2.5 rounded-md px-3 py-2 transition {{ request()->routeIs('dashboard.integrations.*') ? 'bg-orange-50 font-medium text-orange-700 ring-1 ring-orange-100' : 'text-slate-600 hover:bg-white hover:text-slate-900' }}">
                <i class="fa-solid fa-plug w-4 text-center text-xs text-inherit opacity-90" aria-hidden="true"></i>
                Integrations
            </a>
        </div>
    @endif
</nav>
