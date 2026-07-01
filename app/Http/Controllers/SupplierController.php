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
        $suppliers = Supplier::latest()->paginate(10);
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
            'client_type' => 'required|string|in:' . implode(',', array_keys(Supplier::TYPES)),
            'tax_id' => 'required|string|max:30|unique:suppliers,tax_id',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('suppliers', 'public');
        }

        Supplier::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Supplier created successfully.'], 201);
        }

        toast('Proveedor creado exitosamente.', 'success');
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
            'client_type' => 'required|string|in:' . implode(',', array_keys(Supplier::TYPES)),
            'tax_id' => 'required|string|max:30|unique:suppliers,tax_id,' . $supplier->id,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($supplier->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($supplier->photo);
            }
            $validated['photo'] = $request->file('photo')->store('suppliers', 'public');
        } elseif ($request->boolean('remove_photo') && $supplier->photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($supplier->photo);
            $validated['photo'] = null;
        }

        $supplier->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Supplier updated successfully.']);
        }

        toast('Proveedor actualizado exitosamente.', 'success');
        return redirect()->route('suppliers.index');
    }

    public function destroy(Request $request, Supplier $supplier)
    {
        if ($supplier->photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($supplier->photo);
        }
        $supplier->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Supplier deleted successfully.']);
        }

        toast('Proveedor eliminado exitosamente.', 'success');
        return redirect()->route('suppliers.index');
    }
}
