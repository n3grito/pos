<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:report.view-any')->only(['salesByDate', 'topProducts', 'lowStock']);
    }

    public function salesByDate(Request $request)
    {
        $query = Sale::with(['user', 'client', 'branch'])
            ->where('status', 'completed');

        if ($request->filled('from')) {
            $query->whereDate('date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('date', '<=', $request->to);
        }

        $sales = $query->latest()->get();

        $totals = (object) [
            'total_sales' => $sales->count(),
            'total_revenue' => $sales->sum('total'),
            'total_tax' => $sales->sum('tax'),
        ];

        return view('reports.sales', compact('sales', 'totals'));
    }

    public function topProducts(Request $request)
    {
        $limit = $request->get('limit', 10);

        $products = SaleDetail::select(
                'product_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(subtotal) as total_revenue')
            )
            ->whereHas('sale', function ($q) {
                $q->where('status', 'completed');
            })
            ->when($request->filled('from'), function ($q) use ($request) {
                $q->whereHas('sale', function ($sq) use ($request) {
                    $sq->whereDate('date', '>=', $request->from);
                });
            })
            ->when($request->filled('to'), function ($q) use ($request) {
                $q->whereHas('sale', function ($sq) use ($request) {
                    $sq->whereDate('date', '<=', $request->to);
                });
            })
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get()
            ->load('product');

        $topProducts = $products;
        return view('reports.top-products', compact('topProducts'));
    }

    public function lowStock()
    {
        $products = Product::with(['category', 'branch'])
            ->where('is_active', true)
            ->whereColumn('stock', '<=', 'min_stock')
            ->get();

        $lowStockProducts = $products;
        return view('reports.low-stock', compact('lowStockProducts'));
    }
}
