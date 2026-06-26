<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:supplier.view-any')->only('index');
        $this->middleware('can:supplier.view')->only('show');
        $this->middleware('can:supplier.create')->only(['create', 'store']);
        $this->middleware('can:supplier.update')->only(['edit', 'update']);
        $this->middleware('can:supplier.delete')->only('destroy');
    }

    public function index()
    {
        $suppliers = Supplier::paginate(10);

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
        ]);

        Supplier::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Supplier created successfully.'], 201);
        }

        toast('Supplier created successfully.', 'success');
        return redirect()->route('suppliers.index');
    }

    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
        ]);

        $supplier->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Supplier updated successfully.']);
        }

        toast('Supplier updated successfully.', 'success');
        return redirect()->route('suppliers.index');
    }

    public function destroy(Request $request, Supplier $supplier)
    {
        $supplier->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Supplier deleted successfully.']);
        }

        toast('Supplier deleted successfully.', 'success');
        return redirect()->route('suppliers.index');
    }
}
