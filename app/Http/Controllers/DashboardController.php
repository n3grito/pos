<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Client;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $salesToday = Sale::whereDate('date', today())->where('status', 'completed')->sum('total');
        $salesMonth = Sale::whereMonth('date', now()->month)->whereYear('date', now()->year)->where('status', 'completed')->sum('total');
        $totalProducts = Product::count();
        $totalClients = Client::count();
        $lowStock = Product::where('is_active', true)->whereColumn('stock', '<=', 'min_stock')->get();
        $recentSales = Sale::with(['user', 'client', 'branch'])->where('status', 'completed')->latest()->take(10)->get();

        return view('dashboard', compact('salesToday', 'salesMonth', 'totalProducts', 'totalClients', 'lowStock', 'recentSales'));
    }

    public function chartData()
    {
        $days = 30;

        // Daily sales for last 30 days
        $dailySales = Sale::where('status', 'completed')
            ->where('date', '>=', now()->subDays($days)->startOfDay())
            ->selectRaw('DATE(date) as date, SUM(total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $dates = collect();
        for ($i = $days; $i >= 0; $i--) {
            $dates->push(now()->subDays($i)->format('Y-m-d'));
        }

        $dailySalesData = $dates->map(fn ($d) => round((float) ($dailySales[$d] ?? 0), 2));
        $dailySalesLabels = $dates->map(fn ($d) => \Carbon\Carbon::parse($d)->format('d/m'));

        // Sales by payment method
        $paymentMethods = Sale::where('status', 'completed')
            ->selectRaw('payment_method, SUM(total) as total')
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method');

        $methodLabels = [
            'cash' => __('Efectivo'),
            'card' => __('Tarjeta'),
            'transfer' => __('Transferencia'),
            'credit' => __('Crédito'),
        ];

        // Top products
        $topProducts = SaleDetail::selectRaw('products.name, SUM(sale_details.quantity) as total_qty')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->where('sales.date', '>=', now()->subDays($days)->startOfDay())
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->take(10)
            ->pluck('total_qty', 'name');

        // Sales by day of week
        $dayOfWeek = Sale::where('status', 'completed')
            ->selectRaw('DAYOFWEEK(date) as day_num, SUM(total) as total')
            ->groupBy('day_num')
            ->orderBy('day_num')
            ->pluck('total', 'day_num');

        $dayLabels = ['', __('Domingo'), __('Lunes'), __('Martes'), __('Miércoles'), __('Jueves'), __('Viernes'), __('Sábado')];

        return response()->json([
            'dailySales' => [
                'labels' => $dailySalesLabels,
                'data'   => $dailySalesData,
            ],
            'paymentMethods' => [
                'labels' => $paymentMethods->keys()->map(fn ($m) => $methodLabels[$m] ?? $m),
                'data'   => $paymentMethods->values()->map(fn ($v) => round((float) $v, 2)),
            ],
            'topProducts' => [
                'labels' => $topProducts->keys(),
                'data'   => $topProducts->values()->map(fn ($v) => round((float) $v, 1)),
            ],
            'dayOfWeek' => [
                'labels' => $dayOfWeek->keys()->map(fn ($d) => $dayLabels[(int) $d] ?? ''),
                'data'   => $dayOfWeek->values()->map(fn ($v) => round((float) $v, 2)),
            ],
        ]);
    }
}
