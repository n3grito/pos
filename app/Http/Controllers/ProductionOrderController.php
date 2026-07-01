<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:production.view-any')->only('index');
        $this->middleware('can:production.view')->only('show');
        $this->middleware('can:production.create')->only(['create', 'store']);
        $this->middleware('can:production.update')->only(['edit', 'update']);
        $this->middleware('can:production.delete')->only('destroy');
        $this->middleware('can:production.complete')->only('complete');
        $this->middleware('can:production.cancel')->only('cancel');
    }

    public function index()
    {
        $orders = ProductionOrder::with(['product', 'user'])
            ->latest()
            ->paginate(15);
        return view('production.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $rawMaterials = Product::where('is_active', true)->orderBy('name')->get();
        return view('production.create', compact('products', 'rawMaterials'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.001',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
        ]);

        $order = DB::transaction(function () use ($validated) {
            $order = ProductionOrder::create([
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'draft',
                'user_id' => auth()->id(),
            ]);

            $items = [];
            foreach ($validated['items'] as $item) {
                $items[] = new \App\Models\ProductionOrderItem([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }
            $order->items()->saveMany($items);

            return $order;
        });

        toast('Orden de producción creada correctamente.', 'success');
        return redirect()->route('production.index');
    }

    public function show(ProductionOrder $production)
    {
        $production->load(['product', 'items.product', 'user']);
        return view('production.show', compact('production'));
    }

    public function edit(ProductionOrder $production)
    {
        if (!in_array($production->status, ['draft', 'pending'])) {
            toast('Solo se pueden editar órdenes en borrador o pendientes.', 'error');
            return redirect()->route('production.index');
        }

        $production->load('items');
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $rawMaterials = Product::where('is_active', true)->orderBy('name')->get();
        return view('production.edit', compact('production', 'products', 'rawMaterials'));
    }

    public function update(Request $request, ProductionOrder $production)
    {
        if (!in_array($production->status, ['draft', 'pending'])) {
            toast('Solo se pueden editar órdenes en borrador o pendientes.', 'error');
            return redirect()->route('production.index');
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.001',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
        ]);

        DB::transaction(function () use ($production, $validated) {
            $production->update([
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $production->items()->delete();

            $items = [];
            foreach ($validated['items'] as $item) {
                $items[] = new \App\Models\ProductionOrderItem([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }
            $production->items()->saveMany($items);
        });

        toast('Orden de producción actualizada correctamente.', 'success');
        return redirect()->route('production.index');
    }

    public function destroy(ProductionOrder $production)
    {
        if ($production->status === 'completed') {
            toast('No se puede eliminar una orden completada. Cáncela primero.', 'error');
            return redirect()->route('production.index');
        }

        $production->delete();
        toast('Orden de producción eliminada.', 'success');
        return redirect()->route('production.index');
    }

    public function completeForm(ProductionOrder $production)
    {
        if ($production->status !== 'in_progress') {
            toast('La orden debe estar "en producción" para completarse.', 'error');
            return redirect()->route('production.index');
        }

        $production->load('product');
        $warehouses = Warehouse::where('is_active', true)->get();
        return view('production.complete', compact('production', 'warehouses'));
    }

    public function complete(Request $request, ProductionOrder $production)
    {
        if ($production->status !== 'in_progress') {
            toast('La orden debe estar "en producción" para completarse.', 'error');
            return redirect()->route('production.index');
        }

        $validated = $request->validate([
            'produced_quantity' => 'required|numeric|min:0.001',
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);

        try {
            DB::transaction(function () use ($production, $validated) {
                $product = Product::lockForUpdate()->findOrFail($production->product_id);
                $warehouseId = $validated['warehouse_id'];
                $producedQty = $validated['produced_quantity'];
                $reference = 'PROD-' . $production->id;

                foreach ($production->items as $item) {
                    $raw = Product::lockForUpdate()->findOrFail($item->product_id);
                    $needed = $item->quantity * ($producedQty / $production->quantity);

                    if ($raw->stock < $needed) {
                        throw new \Exception("Stock insuficiente de {$raw->name}. Requerido: {$needed}, disponible: {$raw->stock}");
                    }

                    $raw->decrement('stock', $needed);

                    InventoryMovement::create([
                        'product_id' => $raw->id,
                        'warehouse_id' => $warehouseId,
                        'user_id' => auth()->id(),
                        'type' => 'out',
                        'quantity' => $needed,
                        'reference' => $reference,
                        'notes' => "Consumido en producción de: {$product->name} (Orden #{$production->id})",
                    ]);
                }

                $product->increment('stock', $producedQty);

                InventoryMovement::create([
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouseId,
                    'user_id' => auth()->id(),
                    'type' => 'in',
                    'quantity' => $producedQty,
                    'reference' => $reference,
                    'notes' => "Producido (Orden #{$production->id})",
                ]);

                $production->update([
                    'status' => 'completed',
                    'produced_quantity' => $producedQty,
                    'completed_at' => now(),
                ]);

                \App\Models\ActivityLog::create([
                    'action' => 'production_completed',
                    'severity' => 'info',
                    'notable' => true,
                    'description' => "Orden de producción #{$production->id} completada: {$producedQty} x {$product->name}",
                    'user_id' => auth()->id(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            });
        } catch (\Exception $e) {
            toast($e->getMessage(), 'error');
            return redirect()->back()->withInput();
        }

        toast('Producción completada exitosamente.', 'success');
        return redirect()->route('production.index');
    }

    public function cancel(ProductionOrder $production)
    {
        if (in_array($production->status, ['completed', 'cancelled'])) {
            toast('La orden ya está completada o cancelada.', 'error');
            return redirect()->route('production.index');
        }

        DB::transaction(function () use ($production) {
            $production->update([
                'status' => 'cancelled',
                'completed_at' => now(),
            ]);

            \App\Models\ActivityLog::create([
                'action' => 'production_cancelled',
                'severity' => 'warning',
                'notable' => true,
                'description' => "Orden de producción #{$production->id} cancelada: {$production->product->name}",
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        toast('Orden de producción cancelada.', 'success');
        return redirect()->route('production.index');
    }

    public function start(ProductionOrder $production)
    {
        if ($production->status !== 'draft' && $production->status !== 'pending') {
            toast('La orden ya está en producción o completada.', 'error');
            return redirect()->route('production.index');
        }

        $production->update(['status' => 'in_progress']);
        toast('Orden de producción iniciada.', 'success');
        return redirect()->route('production.index');
    }
}
