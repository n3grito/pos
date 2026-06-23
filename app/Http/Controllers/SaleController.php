<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CashRegisterSession;
use App\Models\Client;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:sale.view-any')->only('index');
        $this->middleware('can:sale.view')->only(['show', 'thermal']);
        $this->middleware('can:sale.create')->only(['create', 'store']);
        $this->middleware('can:sale.cancel')->only('cancel');
    }

    public function index(Request $request)
    {
        $query = Sale::with(['user', 'client', 'branch', 'warehouse']);

        if ($request->filled('from')) {
            $query->whereDate('date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('date', '<=', $request->to);
        }

        $sales = $query->latest()->paginate(10);

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $products = Product::with(['category'])->where('is_active', true)->where('available_for_sale', true)->get();
        $clients = Client::where('is_active', true)->get();
        $sessions = CashRegisterSession::with(['cashRegister'])->where('status', 'open')->get();
        $services = Service::with(['products'])->where('is_active', true)->get()->each(function ($service) {
            $service->products_json = $service->products->map(fn($p) => [
                'product_id' => $p->id,
                'name' => $p->name,
                'price' => $p->selling_price,
                'quantity' => $p->pivot->quantity,
                'stock' => $p->stock,
            ])->toJson();
        });

        $items = collect();

        foreach ($products as $p) {
            $items->push((object) [
                'type' => 'product',
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku,
                'price' => $p->selling_price,
                'stock' => $p->stock,
                'min_stock' => $p->min_stock,
                'products_json' => null,
                'components_count' => null,
            ]);
        }

        foreach ($services as $s) {
            $items->push((object) [
                'type' => 'service',
                'id' => $s->id,
                'name' => $s->name,
                'sku' => null,
                'price' => $s->selling_price,
                'stock' => null,
                'min_stock' => null,
                'products_json' => $s->products_json,
                'components_count' => $s->products->count(),
            ]);
        }

        return view('sales.create', compact('products', 'clients', 'sessions', 'services', 'items'));
    }

    public function store(Request $request)
    {
        if ($request->filled('items')) {
            $items = json_decode($request->items, true);
            $request->merge(['details' => is_array($items) ? $items : []]);
        }

        if (empty($request->details)) {
            throw ValidationException::withMessages(['items' => __('Agregue al menos un producto al carrito')]);
        }

        // Pre-validate items and separate products vs services (no stock check yet)
        $productItems = [];
        $serviceItems = [];

        foreach ($request->details as $item) {
            if (isset($item['service_id'])) {
                $service = Service::with(['products'])->findOrFail($item['service_id']);
                $qtyMultiplier = (float) ($item['quantity'] ?? 1);
                $servicePrice = (float) ($item['price'] ?? 0);

                if ($qtyMultiplier < 1) {
                    throw ValidationException::withMessages(['items' => __('Cantidad inválida para el servicio')]);
                }
                if ($servicePrice <= 0) {
                    throw ValidationException::withMessages(['items' => __('Precio inválido para el servicio')]);
                }
                if ($service->products->isEmpty()) {
                    throw ValidationException::withMessages(['items' => __('El servicio :name no tiene productos asignados', ['name' => $service->name])]);
                }

                $serviceItems[] = [
                    'service' => $service,
                    'quantity' => $qtyMultiplier,
                    'price' => $servicePrice,
                ];
            } else {
                $productId = (int) ($item['product_id'] ?? 0);
                $quantity = (float) ($item['quantity'] ?? 0);
                $price = (float) ($item['price'] ?? 0);

                $product = Product::findOrFail($productId);
                if ($quantity < 1) {
                    throw ValidationException::withMessages(['items' => __('Cantidad inválida para :name', ['name' => $product->name])]);
                }
                if ($price <= 0) {
                    throw ValidationException::withMessages(['items' => __('Precio inválido para :name', ['name' => $product->name])]);
                }
                if (!$product->available_for_sale) {
                    throw ValidationException::withMessages([
                        'items' => __(':name no está disponible para la venta', ['name' => $product->name]),
                    ]);
                }

                $productItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'price' => $price,
                ];
            }
        }

        if (empty($productItems) && empty($serviceItems)) {
            throw ValidationException::withMessages(['items' => __('Agregue al menos un producto o servicio al carrito')]);
        }

        $rules = [
            'client_id' => 'nullable|exists:clients,id',
            'cash_register_session_id' => 'nullable|exists:cash_register_sessions,id',
            'payment_method' => 'required|string|in:cash,card,transfer,credit',
        ];

        if ($request->payment_method === 'cash') {
            $rules['amount_paid'] = 'required|numeric|min:0';
        }

        if (in_array($request->payment_method, ['card', 'transfer'])) {
            $rules['payment_reference'] = 'required|string|max:255';
            $rules['client_name'] = 'required|string|max:255';
            $rules['client_nit'] = 'required|digits:11';
        }

        $validated = $request->validate($rules);

        try {
            $sale = DB::transaction(function () use ($validated, $request, $productItems, $serviceItems) {
                $count = Sale::whereDate('created_at', today())->count();
                $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);

                $subtotal = 0;
                $totalTax = 0;

                $amountPaid = null;
                $change = null;

                $user = auth()->user();
                $branchId = $user->branch_id;
                if (!$branchId && $request->filled('cash_register_session_id')) {
                    $session = CashRegisterSession::with('cashRegister')->find($request->cash_register_session_id);
                    $branchId = $session?->cashRegister?->branch_id;
                }
                if (!$branchId) {
                    $branchId = Branch::where('is_active', true)->value('id');
                }
                if (!$branchId) {
                    throw new \RuntimeException('No hay una sucursal activa configurada. Contacte al administrador.');
                }

                $sale = Sale::create([
                    'invoice_number' => $invoiceNumber,
                    'user_id' => auth()->id(),
                    'client_id' => $validated['client_id'] ?? null,
                    'branch_id' => $branchId,
                    'warehouse_id' => $user->warehouse_id,
                    'cash_register_session_id' => $validated['cash_register_session_id'] ?? null,
                    'subtotal' => 0,
                    'tax' => 0,
                    'total' => 0,
                    'amount_paid' => null,
                    'change' => null,
                    'payment_reference' => $request->payment_reference,
                    'client_name' => $request->client_name,
                    'client_nit' => $request->client_nit,
                    'payment_method' => $validated['payment_method'],
                    'status' => 'completed',
                    'date' => today(),
                ]);

                // Process product items (with pessimistic lock)
                foreach ($productItems as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item['product']->id);
                    if ($product->stock < $item['quantity']) {
                        throw ValidationException::withMessages([
                            'items' => __('Stock insuficiente en inventario para :name. Disponible :stock', [
                                'name' => $product->name,
                                'stock' => $product->stock,
                            ]),
                        ]);
                    }

                    $lineSubtotal = $item['quantity'] * $item['price'];
                    $lineTax = round($lineSubtotal * ($product->tax_percentage / 100), 2);
                    $subtotal += $lineSubtotal;
                    $totalTax += $lineTax;

                    SaleDetail::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'subtotal' => $lineSubtotal,
                    ]);

                    $product->decrement('stock', $item['quantity']);

                    InventoryMovement::create([
                        'product_id' => $product->id,
                        'warehouse_id' => $sale->warehouse_id,
                        'user_id' => auth()->id(),
                        'type' => 'out',
                        'quantity' => $item['quantity'],
                        'reference' => $sale->invoice_number,
                        'notes' => 'Venta #' . $sale->invoice_number,
                    ]);
                }

                // Process service items (with pessimistic lock)
                foreach ($serviceItems as $item) {
                    $lineSubtotal = $item['quantity'] * $item['price'];
                    $lineTax = round($lineSubtotal * ($item['service']->tax_percentage / 100), 2);
                    $subtotal += $lineSubtotal;
                    $totalTax += $lineTax;

                    SaleDetail::create([
                        'sale_id' => $sale->id,
                        'service_id' => $item['service']->id,
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'subtotal' => $lineSubtotal,
                    ]);

                    // Deduct stock of component products from inventory (with pessimistic lock)
                    foreach ($item['service']->products as $component) {
                        $lockedComponent = Product::lockForUpdate()->findOrFail($component->id);
                        $componentQty = $component->pivot->quantity * $item['quantity'];

                        if ($lockedComponent->stock < $componentQty) {
                            throw ValidationException::withMessages([
                                'items' => __('Stock insuficiente en inventario para :name. Necesita :need, disponible :stock', [
                                    'name' => $lockedComponent->name,
                                    'need' => $componentQty,
                                    'stock' => $lockedComponent->stock,
                                ]),
                            ]);
                        }

                        $lockedComponent->decrement('stock', $componentQty);

                        InventoryMovement::create([
                            'product_id' => $component->id,
                            'warehouse_id' => $sale->warehouse_id,
                            'user_id' => auth()->id(),
                            'type' => 'out',
                            'quantity' => $componentQty,
                            'reference' => $sale->invoice_number,
                            'notes' => 'Venta #' . $sale->invoice_number . ' (servicio: ' . $item['service']->name . ')',
                        ]);
                    }
                }

                // If payment is cash, validate amount
                if ($request->payment_method === 'cash' && $request->filled('amount_paid')) {
                    $amountPaid = (float) $request->amount_paid;
                    $change = round($amountPaid - $subtotal, 2);
                    if ($change < 0) {
                        throw ValidationException::withMessages([
                            'amount_paid' => __('El monto recibido es menor que el total de la venta. Faltan $') . number_format(abs($change), 2),
                        ]);
                    }
                }

                $sale->update([
                    'subtotal' => $subtotal,
                    'tax' => $totalTax,
                    'total' => $subtotal + $totalTax,
                    'amount_paid' => $amountPaid,
                    'change' => $change,
                ]);

                return $sale;
            });

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Sale created successfully.', 'sale' => $sale], 201);
            }

            session()->flash('success', 'Venta creada exitosamente.');
            return redirect()->route('sales.show', $sale);
        } catch (ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage(), 'errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Failed to create sale.', 'error' => $e->getMessage()], 500);
            }

            session()->flash('error', 'Error al crear la venta. ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function thermal(Sale $sale)
    {
        $sale->load(['user', 'branch', 'warehouse', 'details.product', 'details.service']);

        return view('sales.thermal', compact('sale'));
    }

    public function show(Sale $sale)
    {
        $sale->load(['user', 'client', 'branch', 'warehouse', 'cashRegisterSession', 'details.product', 'details.service']);

        return view('sales.show', compact('sale'));
    }

    public function cancel(Request $request, Sale $sale)
    {
        if ($sale->status === 'cancelled') {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Sale is already cancelled.'], 400);
            }
            session()->flash('error', 'La venta ya está cancelada.');
            return redirect()->back();
        }

        try {
            DB::transaction(function () use ($sale) {
                $sale->update(['status' => 'cancelled']);

                foreach ($sale->details as $detail) {
                    if ($detail->service) {
                        // Restore stock of component products in inventory
                        $service = $detail->service->load('products');
                        foreach ($service->products as $component) {
                            $componentQty = $component->pivot->quantity * $detail->quantity;
                            Product::where('id', $component->id)->increment('stock', $componentQty);

                            InventoryMovement::create([
                                'product_id' => $component->id,
                                'warehouse_id' => $sale->warehouse_id,
                                'user_id' => auth()->id(),
                                'type' => 'in',
                                'quantity' => $componentQty,
                                'reference' => $sale->invoice_number,
                                'notes' => 'Cancelación de venta #' . $sale->invoice_number . ' (servicio: ' . $service->name . ')',
                            ]);
                        }
                    } else {
                        $product = $detail->product;
                        if ($product) {
                            Product::where('id', $detail->product_id)->increment('stock', $detail->quantity);

                            InventoryMovement::create([
                                'product_id' => $detail->product_id,
                                'warehouse_id' => $sale->warehouse_id,
                                'user_id' => auth()->id(),
                                'type' => 'in',
                                'quantity' => $detail->quantity,
                                'reference' => $sale->invoice_number,
                                'notes' => 'Cancelación de venta #' . $sale->invoice_number,
                            ]);
                        }
                    }
                }
            });

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Sale cancelled successfully.']);
            }

            session()->flash('success', 'Venta cancelada exitosamente.');
            return redirect()->route('sales.index');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Failed to cancel sale.', 'error' => $e->getMessage()], 500);
            }

            session()->flash('error', 'Error al cancelar la venta. ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
