<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:inventory.view')->only(['index', 'show']);
        $this->middleware('can:inventory.movements')->only('movements');
        $this->middleware('can:inventory.adjustment')->only(['adjustment', 'storeAdjustment']);
        $this->middleware('can:inventory.low-stock')->only('lowStock');
        $this->middleware('can:product.update')->only('toggleAvailability');
    }

    public function index(Request $request)
    {
        $query = Product::with(['category']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($stockFilter = $request->get('stock')) {
            if ($stockFilter === 'low') {
                $query->whereColumn('stock', '<=', 'min_stock')->where('stock', '>', 0);
            } elseif ($stockFilter === 'out') {
                $query->where('stock', 0);
            } elseif ($stockFilter === 'available') {
                $query->where('stock', '>', 0);
            }
        }

        if ($saleFilter = $request->get('sale')) {
            $query->where('available_for_sale', $saleFilter === 'yes');
        }

        $products = $query->orderBy('name')->paginate(15);

        return view('inventory.index', compact('products'));
    }

    public function show(Product $product)
    {
        $product->load(['category']);

        $warehouseStocks = WarehouseStock::where('product_id', $product->id)
            ->with('warehouse')
            ->get();

        $movements = InventoryMovement::where('product_id', $product->id)
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('inventory.show', compact('product', 'warehouseStocks', 'movements'));
    }

    public function movements(Request $request)
    {
        $query = InventoryMovement::with(['product', 'user']);

        if ($productId = $request->get('product_id')) {
            $query->where('product_id', $productId);
        }

        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $movements = $query->latest()->paginate(20);
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('inventory.movements', compact('movements', 'products'));
    }

    public function adjustment(Request $request)
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        $selectedProductId = $request->get('product_id');

        return view('inventory.adjustment', compact('products', 'warehouses', 'selectedProductId'));
    }

    public function storeAdjustment(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|numeric|min:0.001',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::findOrFail($validated['product_id']);

            if ($validated['type'] === 'in') {
                $product->increment('stock', $validated['quantity']);
            } elseif ($validated['type'] === 'out') {
                if ($product->stock < $validated['quantity']) {
                    throw new \Exception('Stock insuficiente en inventario. Stock actual: ' . $product->stock);
                }
                $product->decrement('stock', $validated['quantity']);
            } else {
                $product->update(['stock' => abs($validated['quantity'])]);
            }

            InventoryMovement::create([
                'product_id' => $validated['product_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'user_id' => auth()->id(),
                'type' => $validated['type'],
                'quantity' => $validated['quantity'],
                'reference' => 'MANUAL-' . now()->format('YmdHis'),
                'notes' => $validated['notes'] ?? 'Ajuste manual de inventario',
            ]);
        });

        toast('Inventario ajustado correctamente.', 'success');
        return redirect()->route('inventory.index');
    }

    public function lowStock()
    {
        $products = Product::with(['category'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->filter(function ($product) {
                return $product->stock <= $product->min_stock;
            })
            ->sortBy('stock')
            ->values();

        return view('inventory.low-stock', compact('products'));
    }

    public function toggleAvailability(Request $request, Product $product)
    {
        $product->update([
            'available_for_sale' => !$product->available_for_sale,
        ]);

        $status = $product->available_for_sale
            ? __('disponible para la venta')
            : __('no disponible para la venta');

        if ($request->wantsJson()) {
            return response()->json([
                'message' => __('Producto :status.', ['status' => $status]),
                'available_for_sale' => $product->available_for_sale,
            ]);
        }

        toast(__('Producto :status.', ['status' => $status]), 'success');
        return redirect()->back();
    }
}
