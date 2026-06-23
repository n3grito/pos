<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:warehouse.view-any')->only('index');
        $this->middleware('can:warehouse.view')->only('show');
        $this->middleware('can:warehouse.create')->only(['create', 'store']);
        $this->middleware('can:warehouse.update')->only(['edit', 'update']);
        $this->middleware('can:warehouse.delete')->only('destroy');
    }

    public function index()
    {
        $warehouses = Warehouse::latest()->paginate(10);
        return view('warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        return view('warehouses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        Warehouse::create($validated);

        session()->flash('success', 'Almacén creado correctamente.');
        return redirect()->route('warehouses.index');
    }

    public function show(Warehouse $warehouse)
    {
        $warehouse->load(['stock.product', 'inventoryMovements' => function ($q) {
            $q->latest()->limit(50);
        }]);

        return view('warehouses.show', compact('warehouse'));
    }

    public function edit(Warehouse $warehouse)
    {
        return view('warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $warehouse->update($validated);

        session()->flash('success', 'Almacén actualizado correctamente.');
        return redirect()->route('warehouses.index');
    }

    public function destroy(Warehouse $warehouse)
    {
        if ($warehouse->stock()->sum('quantity') > 0) {
            return back()->with('error', 'No se puede eliminar un almacén con existencias.');
        }

        $warehouse->delete();

        session()->flash('success', 'Almacén eliminado correctamente.');
        return redirect()->route('warehouses.index');
    }
}
