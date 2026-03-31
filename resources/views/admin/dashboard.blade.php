@extends('layouts.dashboard')

@section('title', 'Overview — '.config('app.name'))
@section('heading', 'Overview')
@section('subheading', 'Store snapshot, sales trends, and quick access.')

@section('content')
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <div class="flex items-start justify-between gap-2">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-500">Orders</p>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-orange-50 text-[#ff6000] ring-1 ring-orange-100"><i class="fa-solid fa-receipt text-sm" aria-hidden="true"></i></span>
            </div>
            <p class="mt-2 text-3xl font-semibold tabular-nums text-slate-900">{{ number_format($ordersCount) }}</p>
            <p class="mt-1 text-sm text-slate-500">{{ $pendingOrders }} pending</p>
        </div>
        <div class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <div class="flex items-start justify-between gap-2">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-500">Revenue (30d)</p>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-orange-50 text-[#ff6000] ring-1 ring-orange-100"><i class="fa-solid fa-chart-line text-sm" aria-hidden="true"></i></span>
            </div>
            <p class="mt-2 text-2xl font-semibold tabular-nums text-slate-900">₵ {{ number_format($revenue30dTotal, 2) }}</p>
            <p class="mt-1 text-sm text-slate-500">Delivered orders</p>
        </div>
        <div class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <div class="flex items-start justify-between gap-2">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-500">Paid (30d)</p>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100"><i class="fa-solid fa-circle-check text-sm" aria-hidden="true"></i></span>
            </div>
            <p class="mt-2 text-2xl font-semibold tabular-nums text-emerald-700">₵ {{ number_format($paidRevenue30d, 2) }}</p>
            <p class="mt-1 text-sm text-slate-500">Marked paid &amp; delivered</p>
        </div>
        <div class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <div class="flex items-start justify-between gap-2">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-500">Products</p>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-orange-50 text-[#ff6000] ring-1 ring-orange-100"><i class="fa-solid fa-box text-sm" aria-hidden="true"></i></span>
            </div>
            <p class="mt-2 text-3xl font-semibold tabular-nums text-slate-900">{{ number_format($productsCount) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)] sm:col-span-2 xl:col-span-1">
            <div class="flex items-start justify-between gap-2">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-500">Paystack</p>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-600 ring-1 ring-slate-200/80"><i class="fa-solid fa-plug text-sm" aria-hidden="true"></i></span>
            </div>
            <p class="mt-2 text-lg font-semibold text-slate-900">{{ paystack_ready() ? 'Ready' : 'Not configured' }}</p>
            <a href="{{ route('dashboard.integrations.edit') }}" class="mt-2 inline-block text-sm font-medium text-[#ff6000] underline decoration-orange-200 underline-offset-2 hover:text-orange-700">Integrations</a>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-6 xl:grid-cols-2">
        <div class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <h2 class="text-base font-semibold text-slate-900">Revenue trend</h2>
            <p class="mt-1 text-sm text-slate-500">Sum of delivered order totals by day (last 30 days).</p>
            <div class="mt-4 h-64">
                <canvas id="chart-revenue" aria-label="Revenue chart"></canvas>
            </div>
        </div>
        <div class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <h2 class="text-base font-semibold text-slate-900">New orders</h2>
            <p class="mt-1 text-sm text-slate-500">Orders created per day (last 14 days).</p>
            <div class="mt-4 h-64">
                <canvas id="chart-orders-trend" aria-label="Orders per day"></canvas>
            </div>
        </div>
        <div class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <h2 class="text-base font-semibold text-slate-900">Delivery pipeline</h2>
            <p class="mt-1 text-sm text-slate-500">All orders by current delivery status.</p>
            <div class="mx-auto mt-2 h-56 max-w-xs">
                <canvas id="chart-delivery" aria-label="Delivery status"></canvas>
            </div>
        </div>
        <div class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <h2 class="text-base font-semibold text-slate-900">Top sellers</h2>
            <p class="mt-1 text-sm text-slate-500">Units sold on delivered orders (all time).</p>
            <div class="mt-4 h-64">
                <canvas id="chart-top-products" aria-label="Top products"></canvas>
            </div>
        </div>
        <div class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)] xl:col-span-2">
            <h2 class="text-base font-semibold text-slate-900">Catalog by category</h2>
            <p class="mt-1 text-sm text-slate-500">Active and inactive products grouped by category.</p>
            <div class="mt-4 h-56 max-w-4xl">
                <canvas id="chart-categories" aria-label="Products by category"></canvas>
            </div>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-12">
        <div class="lg:col-span-8">
            <div class="overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="text-base font-semibold text-slate-900">Shortcuts</h2>
                    <p class="mt-1 text-sm text-slate-500">Jump to the areas you manage most often.</p>
                </div>
                <div class="grid grid-cols-1 gap-px bg-slate-100 sm:grid-cols-2">
                    <a href="{{ route('dashboard.products.index') }}" class="flex items-center gap-3 bg-white px-5 py-4 transition hover:bg-slate-50/90">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg border border-orange-100 bg-orange-50 text-[#ff6000]"><i class="fa-solid fa-box" aria-hidden="true"></i></span>
                        <span>
                            <span class="block font-medium text-slate-900">Products</span>
                            <span class="text-sm text-slate-500">Inventory and pricing</span>
                        </span>
                    </a>
                    <a href="{{ route('dashboard.news-posts.index') }}" class="flex items-center gap-3 bg-white px-5 py-4 transition hover:bg-slate-50/90">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg border border-orange-100 bg-orange-50 text-[#ff6000]"><i class="fa-solid fa-newspaper" aria-hidden="true"></i></span>
                        <span>
                            <span class="block font-medium text-slate-900">Popular news</span>
                            <span class="text-sm text-slate-500">Homepage stories &amp; images</span>
                        </span>
                    </a>
                    <a href="{{ route('dashboard.hero-slides.index') }}" class="flex items-center gap-3 bg-white px-5 py-4 transition hover:bg-slate-50/90">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg border border-orange-100 bg-orange-50 text-[#ff6000]"><i class="fa-solid fa-images" aria-hidden="true"></i></span>
                        <span>
                            <span class="block font-medium text-slate-900">Hero slides</span>
                            <span class="text-sm text-slate-500">Homepage carousel</span>
                        </span>
                    </a>
                    <a href="{{ route('dashboard.homepage-settings.edit') }}" class="flex items-center gap-3 bg-white px-5 py-4 transition hover:bg-slate-50/90">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg border border-orange-100 bg-orange-50 text-[#ff6000]"><i class="fa-solid fa-house-chimney" aria-hidden="true"></i></span>
                        <span>
                            <span class="block font-medium text-slate-900">Homepage</span>
                            <span class="text-sm text-slate-500">Layout and promos</span>
                        </span>
                    </a>
                </div>
            </div>

        </div>
        <div class="lg:col-span-4">
            <div class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
                <h2 class="text-base font-semibold text-slate-900">Low stock</h2>
                <p class="mt-1 text-sm text-slate-500">Active products with 1–5 units left.</p>
                <ul class="mt-4 space-y-2 text-sm">
                    @forelse ($lowStockProducts as $p)
                        <li class="flex items-center justify-between gap-2 border-b border-slate-100 pb-2 last:border-0">
                            <a href="{{ route('dashboard.products.edit', $p) }}" class="min-w-0 truncate font-medium text-[#ff6000] underline decoration-orange-200 underline-offset-2 hover:text-orange-700">{{ $p->name }}</a>
                            <span class="shrink-0 tabular-nums text-amber-700">{{ $p->stock }} left</span>
                        </li>
                    @empty
                        <li class="text-slate-500">Nothing in this band — or no products yet.</li>
                    @endforelse
                </ul>
            </div>
            <div class="mt-6 rounded-xl border border-slate-200/90 bg-white p-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
                <h2 class="text-base font-semibold text-slate-900">Catalog</h2>
                <ul class="mt-4 space-y-2 text-sm">
                    <li>
                        <a href="{{ route('dashboard.categories.index') }}" class="font-medium text-slate-700 underline decoration-slate-200 underline-offset-2 hover:text-[#ff6000]">Categories ({{ number_format($categoriesCount) }})</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard.integrations.edit') }}" class="font-medium text-slate-700 underline decoration-slate-200 underline-offset-2 hover:text-[#ff6000]">Integrations &amp; API keys</a>
                    </li>
                    <li>
                        <a href="{{ route('home') }}" class="font-medium text-slate-700 underline decoration-slate-200 underline-offset-2 hover:text-[#ff6000]">Open storefront</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" crossorigin="anonymous"></script>
    <script>
        (function () {
            if (typeof Chart === 'undefined') return;
            var accent = '#ff6000';
            var accentSoft = 'rgba(255, 96, 0, 0.12)';
            var grid = 'rgba(15, 23, 42, 0.06)';

            var revenueLabels = @json($revenueLabels);
            var revenueValues = @json($revenueValues);
            var orderTrendLabels = @json($orderTrendLabels);
            var orderTrendValues = @json($orderTrendValues);
            var deliveryLabels = @json($deliveryPieLabels);
            var deliveryValues = @json($deliveryPieValues);
            var topLabels = @json($topProductLabels);
            var topQty = @json($topProductQty);
            var catLabels = @json($categoryBarLabels);
            var catValues = @json($categoryBarValues);

            var pieColors = ['#ff6000', '#fb923c', '#f97316', '#ea580c', '#fdba74', '#94a3b8'];

            function defaultOpts() {
                return {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { color: grid }, ticks: { maxRotation: 45, minRotation: 0, font: { size: 10 } } },
                        y: { grid: { color: grid }, ticks: { font: { size: 10 } }, beginAtZero: true }
                    }
                };
            }

            var revEl = document.getElementById('chart-revenue');
            if (revEl && revenueLabels.length) {
                new Chart(revEl, {
                    type: 'line',
                    data: {
                        labels: revenueLabels,
                        datasets: [{
                            label: 'Revenue (₵)',
                            data: revenueValues,
                            borderColor: accent,
                            backgroundColor: accentSoft,
                            fill: true,
                            tension: 0.35,
                            pointRadius: 0,
                            borderWidth: 2
                        }]
                    },
                    options: defaultOpts()
                });
            }

            var ordEl = document.getElementById('chart-orders-trend');
            if (ordEl && orderTrendLabels.length) {
                new Chart(ordEl, {
                    type: 'bar',
                    data: {
                        labels: orderTrendLabels,
                        datasets: [{
                            label: 'Orders',
                            data: orderTrendValues,
                            backgroundColor: accentSoft,
                            borderColor: accent,
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: defaultOpts()
                });
            }

            var delEl = document.getElementById('chart-delivery');
            if (delEl && deliveryLabels.length) {
                new Chart(delEl, {
                    type: 'doughnut',
                    data: {
                        labels: deliveryLabels,
                        datasets: [{
                            data: deliveryValues,
                            backgroundColor: deliveryLabels.map(function (_, i) { return pieColors[i % pieColors.length]; }),
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } } }
                    }
                });
            }

            var topEl = document.getElementById('chart-top-products');
            if (topEl && topLabels.length) {
                new Chart(topEl, {
                    type: 'bar',
                    data: {
                        labels: topLabels,
                        datasets: [{
                            label: 'Units sold',
                            data: topQty,
                            backgroundColor: pieColors[2],
                            borderRadius: 4
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { grid: { color: grid }, beginAtZero: true, ticks: { font: { size: 10 } } },
                            y: { grid: { display: false }, ticks: { font: { size: 10 } } }
                        }
                    }
                });
            }

            var catEl = document.getElementById('chart-categories');
            if (catEl && catLabels.length) {
                new Chart(catEl, {
                    type: 'bar',
                    data: {
                        labels: catLabels,
                        datasets: [{
                            label: 'Products',
                            data: catValues,
                            backgroundColor: accent,
                            borderRadius: 6
                        }]
                    },
                    options: defaultOpts()
                });
            }
        })();
    </script>
@endpush
