<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:report.view-any')->only(['salesByDate', 'topProducts', 'lowStock', 'exportPdf', 'exportCsv']);
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

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $sales = $query->latest()->get();

        $totals = (object) [
            'total_sales' => $sales->count(),
            'total_revenue' => $sales->sum('total'),
            'total_tax' => $sales->sum('tax'),
            'average' => $sales->count() > 0 ? $sales->sum('total') / $sales->count() : 0,
            'cash_total' => $sales->where('payment_method', 'cash')->sum('total'),
            'card_total' => $sales->where('payment_method', 'card')->sum('total'),
            'transfer_total' => $sales->where('payment_method', 'transfer')->sum('total'),
            'credit_total' => $sales->where('payment_method', 'credit')->sum('total'),
        ];

        $paymentMethods = ['cash' => __('Efectivo'), 'card' => __('Tarjeta'), 'transfer' => __('Transferencia'), 'credit' => __('Crédito')];
        $branches = Branch::where('is_active', true)->get();
        $users = User::all();

        $dailyChartData = $sales->groupBy(fn($s) => $s->date->format('Y-m-d'))
            ->map(fn($items, $date) => ['date' => $date, 'total' => $items->sum('total')])
            ->sortBy('date')
            ->values();

        return view('reports.sales', compact('sales', 'totals', 'paymentMethods', 'branches', 'users', 'dailyChartData'));
    }

    public function topProducts(Request $request)
    {
        $limit = $request->get('limit', 10);

        $query = SaleDetail::select(
                'sale_details.product_id',
                DB::raw('SUM(sale_details.quantity) as total_quantity'),
                DB::raw('SUM(sale_details.subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT sale_details.sale_id) as times_sold')
            )
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->when($request->filled('from'), fn($q) => $q->whereDate('sales.date', '>=', $request->from))
            ->when($request->filled('to'), fn($q) => $q->whereDate('sales.date', '<=', $request->to))
            ->when($request->filled('category_id'), fn($q) => $q->join('products', 'sale_details.product_id', '=', 'products.id')->where('products.category_id', $request->category_id))
            ->groupBy('sale_details.product_id')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get()
            ->load('product.category');

        $topProducts = $query;
        $categories = \App\Models\Category::all();

        return view('reports.top-products', compact('topProducts', 'categories'));
    }

    public function lowStock()
    {
        $products = Product::with(['category', 'branch'])
            ->where('is_active', true)
            ->whereColumn('stock', '<=', 'min_stock')
            ->orderBy('stock')
            ->get();

        $totalLowStock = $products->count();
        $totalMissing = $products->sum(fn($p) => max(0, $p->min_stock - $p->stock));

        return view('reports.low-stock', compact('products', 'totalLowStock', 'totalMissing'));
    }

    public function exportPdf(Request $request, string $type)
    {
        $data = match ($type) {
            'sales' => $this->getSalesData($request),
            'top-products' => $this->getTopProductsData($request),
            'low-stock' => $this->getLowStockData(),
            default => abort(404),
        };

        $pdf = Pdf::loadView('reports.pdf.' . str_replace('-', '_', $type), $data);
        $pdf->setPaper('letter', $type === 'low-stock' ? 'legal' : 'landscape');

        return $pdf->download('reporte-' . $type . '-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportCsv(Request $request, string $type)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="reporte-' . $type . '-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($request, $type) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            match ($type) {
                'sales' => $this->writeSalesCsv($handle, $request),
                'top-products' => $this->writeTopProductsCsv($handle, $request),
                'low-stock' => $this->writeLowStockCsv($handle),
                default => null,
            };

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function getSalesData(Request $request): array
    {
        $sales = $this->buildSalesQuery($request)->get();
        $totals = (object) [
            'total_sales' => $sales->count(),
            'total_revenue' => $sales->sum('total'),
            'total_tax' => $sales->sum('tax'),
            'average' => $sales->count() > 0 ? $sales->sum('total') / $sales->count() : 0,
        ];
        return compact('sales', 'totals');
    }

    protected function getTopProductsData(Request $request): array
    {
        $topProducts = SaleDetail::select('sale_details.product_id', DB::raw('SUM(sale_details.quantity) as total_quantity'), DB::raw('SUM(sale_details.subtotal) as total_revenue'))
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->when($request->filled('from'), fn($q) => $q->whereDate('sales.date', '>=', $request->from))
            ->when($request->filled('to'), fn($q) => $q->whereDate('sales.date', '<=', $request->to))
            ->groupBy('sale_details.product_id')->orderByDesc('total_quantity')->limit(50)->get()->load('product');
        return compact('topProducts');
    }

    protected function getLowStockData(): array
    {
        $products = Product::with('category')->where('is_active', true)->whereColumn('stock', '<=', 'min_stock')->orderBy('stock')->get();
        return compact('products');
    }

    protected function buildSalesQuery(Request $request)
    {
        $query = Sale::with(['user', 'client', 'branch'])->where('status', 'completed');
        if ($request->filled('from')) $query->whereDate('date', '>=', $request->from);
        if ($request->filled('to')) $query->whereDate('date', '<=', $request->to);
        if ($request->filled('payment_method')) $query->where('payment_method', $request->payment_method);
        if ($request->filled('branch_id')) $query->where('branch_id', $request->branch_id);
        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);
        return $query->latest();
    }

    protected function writeSalesCsv($handle, Request $request): void
    {
        fputcsv($handle, ['Factura', 'Cliente', 'Usuario', 'Subtotal', 'Impuesto', 'Total', 'Método', 'Fecha']);
        $this->buildSalesQuery($request)->chunk(100, function ($sales) use ($handle) {
            foreach ($sales as $s) {
                fputcsv($handle, [
                    $s->invoice_number,
                    $s->client?->name ?? $s->client_name ?? 'General',
                    $s->user?->name ?? '-',
                    $s->subtotal,
                    $s->tax,
                    $s->total,
                    $s->payment_method,
                    $s->created_at->format('d/m/Y H:i'),
                ]);
            }
        });
    }

    protected function writeTopProductsCsv($handle, Request $request): void
    {
        fputcsv($handle, ['Producto', 'SKU', 'Categoría', 'Cantidad Vendida', 'Total Ventas']);
        $items = SaleDetail::select('sale_details.product_id', DB::raw('SUM(sale_details.quantity) as total_quantity'), DB::raw('SUM(sale_details.subtotal) as total_revenue'))
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->when($request->filled('from'), fn($q) => $q->whereDate('sales.date', '>=', $request->from))
            ->when($request->filled('to'), fn($q) => $q->whereDate('sales.date', '<=', $request->to))
            ->groupBy('sale_details.product_id')->orderByDesc('total_quantity')->get()->load('product.category');
        foreach ($items as $item) {
            fputcsv($handle, [$item->product?->name ?? '-', $item->product?->sku ?? '-', $item->product?->category?->name ?? '-', $item->total_quantity, $item->total_revenue]);
        }
    }

    protected function writeLowStockCsv($handle): void
    {
        fputcsv($handle, ['Producto', 'SKU', 'Categoría', 'Stock Actual', 'Stock Mínimo', 'Diferencia']);
        $products = Product::with('category')->where('is_active', true)->whereColumn('stock', '<=', 'min_stock')->orderBy('stock')->get();
        foreach ($products as $p) {
            fputcsv($handle, [$p->name, $p->sku, $p->category?->name ?? '-', $p->stock, $p->min_stock, $p->stock - $p->min_stock]);
        }
    }
}
