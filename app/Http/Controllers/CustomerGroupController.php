<?php

namespace App\Http\Controllers;

use App\Models\CustomerGroup;
use Illuminate\Http\Request;

class CustomerGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:customer-group.view-any')->only('index');
        $this->middleware('can:customer-group.create')->only(['create', 'store']);
        $this->middleware('can:customer-group.update')->only(['edit', 'update']);
        $this->middleware('can:customer-group.delete')->only('destroy');
    }

    public function index()
    {
        $groups = CustomerGroup::withCount('clients')->latest()->paginate(10);
        return view('customer-groups.index', compact('groups'));
    }

    public function create()
    {
        return view('customer-groups.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'color' => 'required|string|max:7',
            'is_default' => 'boolean',
        ]);

        if ($validated['is_default'] ?? false) {
            CustomerGroup::where('is_default', true)->update(['is_default' => false]);
        }

        CustomerGroup::create($validated);

        toast('Grupo creado exitosamente.', 'success');
        return redirect()->route('customer-groups.index');
    }

    public function edit(CustomerGroup $customerGroup)
    {
        return view('customer-groups.edit', compact('customerGroup'));
    }

    public function update(Request $request, CustomerGroup $customerGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'color' => 'required|string|max:7',
            'is_default' => 'boolean',
        ]);

        if ($validated['is_default'] ?? false) {
            CustomerGroup::where('is_default', true)->where('id', '!=', $customerGroup->id)->update(['is_default' => false]);
        }

        $customerGroup->update($validated);

        toast('Grupo actualizado exitosamente.', 'success');
        return redirect()->route('customer-groups.index');
    }

    public function destroy(CustomerGroup $customerGroup)
    {
        if ($customerGroup->clients()->count() > 0) {
            toast('No se puede eliminar un grupo con clientes asignados.', 'error', true);
            return redirect()->back();
        }

        $customerGroup->delete();

        toast('Grupo eliminado exitosamente.', 'success');
        return redirect()->route('customer-groups.index');
    }
}
