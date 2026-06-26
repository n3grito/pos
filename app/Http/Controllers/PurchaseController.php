<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:purchase.view-any')->only(['index', 'kanban']);
        $this->middleware('can:purchase.view')->only('show');
        $this->middleware('can:purchase.create')->only(['create', 'store']);
        $this->middleware('can:purchase.update')->only(['edit', 'update']);
        $this->middleware('can:purchase.delete')->only('destroy');
        $this->middleware('can:purchase.cancel')->only('cancel');
    }

    public function index(Request $request)
    {
        $query = Purchase::with(['supplier', 'user', 'warehouse']);

        if ($search = $request->get('search')) {
            $query->where('invoice_number', 'like', "%{$search}%");
        }

        $purchases = $query->latest()->paginate(10);

        return view('purchases.index', compact('purchases'));
    }

    public function kanban(Request $request)
    {
        $query = Purchase::with(['supplier', 'user', 'warehouse']);

        if ($search = $request->get('search')) {
            $query->where('invoice_number', 'like', "%{$search}%");
        }

        $purchases = $query->latest()->get();
        $pending = $purchases->where('status', 'pending');
        $completed = $purchases->where('status', 'completed');
        $cancelled = $purchases->where('status', 'cancelled');

        return view('purchases.kanban', compact('pending', 'completed', 'cancelled'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();

        return view('purchases.create', compact('suppliers', 'products', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'date' => 'required|date',
            'details' => 'required|array|min:1',
            'details.*.product_id' => 'required|exists:products,id',
            'details.*.quantity' => 'required|numeric|min:0.001',
            'details.*.cost_price' => 'required|numeric|min:0',
        ]);

        try {
            $purchase = DB::transaction(function () use ($validated) {
                $count = Purchase::whereDate('created_at', today())->count();
                $invoiceNumber = 'PUR-' . date('Ymd') . '-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);

                $subtotal = 0;
                $tax = 0;
                foreach ($validated['details'] as $detail) {
                    $lineSubtotal = $detail['quantity'] * $detail['cost_price'];
                    $subtotal += $lineSubtotal;
                    $product = Product::find($detail['product_id']);
                    if ($product && $product->tax_percentage > 0) {
                        $tax += round($lineSubtotal * ($product->tax_percentage / 100), 2);
                    }
                }
                $total = $subtotal + $tax;

                $purchase = Purchase::create([
                    'invoice_number' => $invoiceNumber,
                    'supplier_id' => $validated['supplier_id'],
                    'user_id' => auth()->id(),
                    'warehouse_id' => $validated['warehouse_id'],
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'total' => $total,
                    'status' => 'completed',
                    'date' => $validated['date'],
                ]);

                foreach ($validated['details'] as $detail) {
                    $lineSubtotal = $detail['quantity'] * $detail['cost_price'];

                    PurchaseDetail::create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $detail['product_id'],
                        'quantity' => $detail['quantity'],
                        'cost_price' => $detail['cost_price'],
                        'subtotal' => $lineSubtotal,
                    ]);

                    WarehouseStock::updateOrCreate(
                        ['warehouse_id' => $validated['warehouse_id'], 'product_id' => $detail['product_id']],
                        ['quantity' => DB::raw('quantity + ' . $detail['quantity'])]
                    );

                    InventoryMovement::create([
                        'product_id' => $detail['product_id'],
                        'warehouse_id' => $validated['warehouse_id'],
                        'user_id' => auth()->id(),
                        'type' => 'warehouse_entry',
                        'quantity' => $detail['quantity'],
                        'reference' => $purchase->invoice_number,
                        'notes' => 'Entrada por compra #' . $purchase->invoice_number,
                    ]);
                }

                return $purchase;
            });

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Purchase created successfully.', 'purchase' => $purchase], 201);
            }

            toast('Compra registrada correctamente.', 'success');
            return redirect()->route('purchases.show', $purchase);
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Failed to create purchase.', 'error' => $e->getMessage()], 500);
            }

            toast('Error al registrar la compra. ' . $e->getMessage(), 'error', true);
            return redirect()->back()->withInput();
        }
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'user', 'warehouse', 'details.product']);

        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        //
    }

    public function update(Request $request, Purchase $purchase)
    {
        if ($purchase->status === 'cancelled') {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Purchase is already cancelled.'], 400);
            }
            toast('La compra ya está cancelada.', 'error', true);
            return redirect()->back();
        }

        try {
            DB::transaction(function () use ($purchase) {
                $purchase->update(['status' => 'cancelled']);

                foreach ($purchase->details as $detail) {
                    $ws = WarehouseStock::where('warehouse_id', $purchase->warehouse_id)
                        ->where('product_id', $detail->product_id)
                        ->first();
                    if ($ws) {
                        $ws->decrement('quantity', $detail->quantity);
                    }

                    InventoryMovement::create([
                        'product_id' => $detail->product_id,
                        'warehouse_id' => $purchase->warehouse_id,
                        'user_id' => auth()->id(),
                        'type' => 'warehouse_exit',
                        'quantity' => $detail->quantity,
                        'reference' => $purchase->invoice_number,
                        'notes' => 'Cancelación de compra #' . $purchase->invoice_number,
                    ]);
                }
            });

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Purchase cancelled successfully.']);
            }

            toast('Compra cancelada correctamente.', 'success');
            return redirect()->route('purchases.index');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Failed to cancel purchase.', 'error' => $e->getMessage()], 500);
            }

            toast('Error al cancelar la compra. ' . $e->getMessage(), 'error', true);
            return redirect()->back();
        }
    }

    public function destroy(Purchase $purchase)
    {
        //
    }
}
