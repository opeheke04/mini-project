<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $inactiveProducts = Product::where('is_active', false)->count();
        $stockProducts = Product::sum('stock');
        $lowStockThreshold = config('inventory.low_stock_threshold');
        $lowStockQuery = Product::query()
            ->where('is_active', true)
            ->where('stock', '<=', $lowStockThreshold);
        $lowStockCount = (clone $lowStockQuery)->count();
        $lowStockProducts = (clone $lowStockQuery)
            ->orderBy('stock')
            ->orderBy('name')
            ->limit(6)
            ->get(['id', 'name', 'stock']);

        $today = now()->startOfDay();
        $todaySummary = Sale::query()
            ->whereBetween('created_at', [$today, (clone $today)->endOfDay()])
            ->selectRaw('COUNT(*) as transaction_count, COALESCE(SUM(total), 0) as revenue')
            ->first();
        $monthSummary = Sale::query()
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->selectRaw('COUNT(*) as transaction_count, COALESCE(SUM(total), 0) as revenue')
            ->first();

        $trendFrom = now()->subDays(13)->startOfDay();
        $dailyRevenue = Sale::query()
            ->whereBetween('created_at', [$trendFrom, now()])
            ->selectRaw('DATE(created_at) as bucket, COALESCE(SUM(total), 0) as revenue')
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get()
            ->keyBy('bucket');
        $salesTrend = collect(range(6, 0))
            ->map(function (int $daysAgo) use ($dailyRevenue): array {
                $date = now()->subDays($daysAgo);
                $bucket = $date->toDateString();
                $previousBucket = $date->copy()->subDays(7)->toDateString();

                return [
                    'date' => $date,
                    'revenue' => (float) ($dailyRevenue->get($bucket)?->revenue ?? 0),
                    'previous_revenue' => (float) ($dailyRevenue->get($previousBucket)?->revenue ?? 0),
                ];
            });
        $currentWeekRevenue = (float) $salesTrend->sum('revenue');
        $previousWeekRevenue = (float) $salesTrend->sum('previous_revenue');
        $weeklyChangePercent = $previousWeekRevenue > 0
            ? (($currentWeekRevenue - $previousWeekRevenue) / $previousWeekRevenue) * 100
            : null;

        $topProducts = SaleItem::query()
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereBetween('sales.created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->select('sale_items.product_name')
            ->selectRaw('SUM(sale_items.quantity) as units_sold, SUM(sale_items.line_total) as revenue')
            ->groupBy('sale_items.product_name')
            ->orderByDesc('units_sold')
            ->limit(5)
            ->get();

        $recentSales = Sale::query()
            ->with('user:id,name')
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalProducts',
            'activeProducts',
            'inactiveProducts',
            'stockProducts',
            'lowStockThreshold',
            'lowStockCount',
            'lowStockProducts',
            'todaySummary',
            'monthSummary',
            'salesTrend',
            'currentWeekRevenue',
            'previousWeekRevenue',
            'weeklyChangePercent',
            'topProducts',
            'recentSales'
        ));
    }
}
