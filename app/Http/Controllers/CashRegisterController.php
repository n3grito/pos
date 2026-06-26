<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CashRegister;
use Illuminate\Http\Request;

class CashRegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:cash-register.view-any')->only('index');
        $this->middleware('can:cash-register.view')->only('show');
        $this->middleware('can:cash-register.create')->only(['create', 'store']);
        $this->middleware('can:cash-register.update')->only(['edit', 'update']);
        $this->middleware('can:cash-register.delete')->only('destroy');
    }

    public function index()
    {
        $cashRegisters = CashRegister::with('branch')->paginate(10);

        return view('cash-registers.index', compact('cashRegisters'));
    }

    public function create()
    {
        $branches = Branch::where('is_active', true)->get();

        return view('cash-registers.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
        ]);

        CashRegister::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Cash register created successfully.'], 201);
        }

        toast('Cash register created successfully.', 'success');
        return redirect()->route('cash-registers.index');
    }

    public function show(CashRegister $cashRegister)
    {
        $cashRegister->load('branch');

        return view('cash-registers.show', compact('cashRegister'));
    }

    public function edit(CashRegister $cashRegister)
    {
        return view('cash-registers.edit', compact('cashRegister'));
    }

    public function update(Request $request, CashRegister $cashRegister)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
        ]);

        $cashRegister->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Cash register updated successfully.']);
        }

        toast('Cash register updated successfully.', 'success');
        return redirect()->route('cash-registers.index');
    }

    public function destroy(Request $request, CashRegister $cashRegister)
    {
        $cashRegister->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Cash register deleted successfully.']);
        }

        toast('Cash register deleted successfully.', 'success');
        return redirect()->route('cash-registers.index');
    }
}
