<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Client;
use App\Models\Sale;
use Illuminate\Http\Request;

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
}
