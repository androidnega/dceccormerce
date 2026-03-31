<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        if (auth()->user()->role === 'manager') {
            return $this->managerDashboard();
        }

        $ordersCount = Order::query()->count();
        $pendingOrders = Order::query()->where('delivery_status', 'pending')->count();
        $productsCount = Product::query()->count();
        $categoriesCount = Category::query()->count();

        $revenueLabels = [];
        $revenueValues = [];
        for ($i = 29; $i >= 0; $i--) {
            $day = now()->subDays($i)->startOfDay();
            $revenueLabels[] = $day->format('M j');
            $revenueValues[] = round((float) Order::query()
                ->where('delivery_status', 'delivered')
                ->whereBetween('created_at', [$day, $day->copy()->endOfDay()])
                ->sum('total_amount'), 2);
        }
        $revenue30dTotal = array_sum($revenueValues);

        $orderTrendLabels = [];
        $orderTrendValues = [];
        for ($i = 13; $i >= 0; $i--) {
            $day = now()->subDays($i)->startOfDay();
            $orderTrendLabels[] = $day->format('D j');
            $orderTrendValues[] = Order::query()
                ->whereBetween('created_at', [$day, $day->copy()->endOfDay()])
                ->count();
        }

        $deliveryStatusCounts = Order::query()
            ->selectRaw('delivery_status, COUNT(*) as c')
            ->groupBy('delivery_status')
            ->pluck('c', 'delivery_status');

        $deliveryPieLabels = [];
        $deliveryPieValues = [];
        foreach ($deliveryStatusCounts as $status => $count) {
            $deliveryPieLabels[] = Str::headline(str_replace('_', ' ', (string) $status));
            $deliveryPieValues[] = (int) $count;
        }

        $topRows = OrderItem::query()
            ->selectRaw('product_id, SUM(quantity) as qty')
            ->whereHas('order', fn ($q) => $q->where('delivery_status', 'delivered'))
            ->groupBy('product_id')
            ->orderByDesc('qty')
            ->limit(8)
            ->get();

        $productIds = $topRows->pluck('product_id')->filter()->all();
        $productNames = $productIds === []
            ? collect()
            : Product::query()->whereIn('id', $productIds)->pluck('name', 'id');

        $topProductLabels = [];
        $topProductQty = [];
        foreach ($topRows as $row) {
            $name = $productNames->get($row->product_id);
            $topProductLabels[] = $name ? Str::limit((string) $name, 32) : 'Product #'.$row->product_id;
            $topProductQty[] = (int) $row->qty;
        }

        $categoryBars = Product::query()
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->selectRaw('categories.name as cat_name, COUNT(products.id) as c')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('c')
            ->get();

        $categoryBarLabels = $categoryBars->pluck('cat_name')->map(fn ($n) => Str::limit((string) $n, 24))->all();
        $categoryBarValues = $categoryBars->pluck('c')->map(fn ($v) => (int) $v)->all();

        $lowStockProducts = Product::query()
            ->active()
            ->where('stock', '>', 0)
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->limit(10)
            ->get(['id', 'name', 'stock', 'slug']);

        $paidRevenue = (float) Order::query()
            ->where('delivery_status', 'delivered')
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('total_amount');

        return view('admin.dashboard', [
            'ordersCount' => $ordersCount,
            'pendingOrders' => $pendingOrders,
            'productsCount' => $productsCount,
            'categoriesCount' => $categoriesCount,
            'revenueLabels' => $revenueLabels,
            'revenueValues' => $revenueValues,
            'revenue30dTotal' => $revenue30dTotal,
            'paidRevenue30d' => $paidRevenue,
            'orderTrendLabels' => $orderTrendLabels,
            'orderTrendValues' => $orderTrendValues,
            'deliveryPieLabels' => $deliveryPieLabels,
            'deliveryPieValues' => $deliveryPieValues,
            'topProductLabels' => $topProductLabels,
            'topProductQty' => $topProductQty,
            'categoryBarLabels' => $categoryBarLabels,
            'categoryBarValues' => $categoryBarValues,
            'lowStockProducts' => $lowStockProducts,
        ]);
    }

    private function managerDashboard(): View
    {
        $pendingOrders = Order::query()->where('delivery_status', 'pending')->count();
        $recentOrders = Order::query()
            ->with('user')
            ->orderByDesc('id')
            ->limit(15)
            ->get(['id', 'order_number', 'total_amount', 'delivery_status', 'created_at', 'user_id']);

        return view('admin.manager-dashboard', [
            'pendingOrders' => $pendingOrders,
            'recentOrders' => $recentOrders,
        ]);
    }
}
