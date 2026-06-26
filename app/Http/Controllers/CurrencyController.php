<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:currency.view-any')->only('index');
        $this->middleware('can:currency.create')->only(['create', 'store']);
        $this->middleware('can:currency.update')->only(['edit', 'update', 'toggleActive']);
        $this->middleware('can:currency.delete')->only('destroy');
    }

    public function index()
    {
        $currencies = Currency::orderBy('name')->paginate(10);

        return view('currencies.index', compact('currencies'));
    }

    public function create()
    {
        return view('currencies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:currencies,code',
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0',
        ]);

        Currency::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Moneda creada correctamente.'], 201);
        }

        toast('Moneda creada correctamente.', 'success');
        return redirect()->route('currencies.index');
    }

    public function edit(Currency $currency)
    {
        return view('currencies.edit', compact('currency'));
    }

    public function update(Request $request, Currency $currency)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:currencies,code,' . $currency->id,
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0',
        ]);

        $currency->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Moneda actualizada correctamente.']);
        }

        toast('Moneda actualizada correctamente.', 'success');
        return redirect()->route('currencies.index');
    }

    public function toggleActive(Request $request, Currency $currency)
    {
        $currency->update(['is_active' => !$currency->is_active]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Estado de moneda actualizado.']);
        }

        toast('Estado de moneda actualizado.', 'success');
        return redirect()->route('currencies.index');
    }

    public function destroy(Request $request, Currency $currency)
    {
        $currency->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Moneda eliminada correctamente.']);
        }

        toast('Moneda eliminada correctamente.', 'success');
        return redirect()->route('currencies.index');
    }
}
