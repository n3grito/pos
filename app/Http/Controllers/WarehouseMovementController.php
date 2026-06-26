<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseMovementController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:warehouse.stock')->only('stockByWarehouse');
        $this->middleware('can:warehouse.view')->only('getProducts');
        $this->middleware('can:warehouse.transfer')->only(['transferCreate', 'transferStore']);
    }

    public function stockByWarehouse(Request $request)
    {
        $warehouses = Warehouse::where('is_active', true)->get();
        $selectedWarehouseId = $request->get('warehouse_id');

        $stock = collect();
        if ($selectedWarehouseId) {
            $stock = WarehouseStock::with('product')
                ->where('warehouse_id', $selectedWarehouseId)
                ->where('quantity', '>', 0)
                ->get();
        }

        return view('warehouses.stock', compact('warehouses', 'stock', 'selectedWarehouseId'));
    }

    public function getProducts(Warehouse $warehouse)
    {
        $stock = WarehouseStock::with('product')
            ->where('warehouse_id', $warehouse->id)
            ->where('quantity', '>', 0)
            ->get();

        return response()->json($stock);
    }

    public function transferCreate()
    {
        $warehouses = Warehouse::where('is_active', true)->get();

        return view('warehouses.transfer', compact('warehouses'));
    }

    public function transferStore(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                foreach ($validated['items'] as $item) {
                    $stock = WarehouseStock::where('warehouse_id', $validated['warehouse_id'])
                        ->where('product_id', $item['product_id'])
                        ->first();

                    if (!$stock || $stock->quantity < $item['quantity']) {
                        $product = Product::find($item['product_id']);
                        throw new \Exception("Stock insuficiente en el almacén para {$product->name}. Disponible: " . ($stock->quantity ?? 0) . ", requerido: {$item['quantity']}");
                    }

                    $stock->decrement('quantity', $item['quantity']);

                    Product::where('id', $item['product_id'])->increment('stock', $item['quantity']);

                    InventoryMovement::create([
                        'product_id' => $item['product_id'],
                        'warehouse_id' => $validated['warehouse_id'],
                        'user_id' => auth()->id(),
                        'type' => 'transfer',
                        'quantity' => $item['quantity'],
                        'reference' => $validated['reference'] ?? 'TRF-' . now()->format('YmdHis'),
                        'notes' => $validated['notes'] ?? 'Transferencia de almacén a inventario',
                    ]);
                }
            });

            toast('Transferencia a inventario realizada correctamente.', 'success');
            return redirect()->route('inventory.movements');
        } catch (\Exception $e) {
            toast('Error en la transferencia: ' . $e->getMessage(), 'error', true);
            return back()->withInput();
        }
    }
}
