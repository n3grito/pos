<?php

namespace App\Http\Controllers;

use App\Models\PriceList;
use App\Models\Product;
use Illuminate\Http\Request;

class PriceListController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:setting.manage');
    }

    public function index()
    {
        $priceLists = PriceList::withCount('products')->get();
        return view('price-lists.index', compact('priceLists'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get();
        return view('price-lists.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_default' => 'nullable|boolean',
            'prices' => 'nullable|array',
            'prices.*' => 'nullable|numeric|min:0',
        ]);

        if ($validated['is_default'] ?? false) {
            PriceList::where('is_default', true)->update(['is_default' => false]);
        }

        $priceList = PriceList::create([
            'name' => $validated['name'],
            'is_default' => $validated['is_default'] ?? false,
        ]);

        if (!empty($validated['prices'])) {
            $sync = [];
            foreach ($validated['prices'] as $productId => $price) {
                if ($price !== null && $price !== '') {
                    $sync[$productId] = ['price' => $price];
                }
            }
            $priceList->products()->sync($sync);
        }

        toast('Lista de precio creada.', 'success');
        return redirect()->route('price-lists.index');
    }

    public function edit(PriceList $priceList)
    {
        $products = Product::where('is_active', true)->get();
        $priceList->load('products');
        return view('price-lists.edit', compact('priceList', 'products'));
    }

    public function update(Request $request, PriceList $priceList)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_default' => 'nullable|boolean',
            'prices' => 'nullable|array',
            'prices.*' => 'nullable|numeric|min:0',
        ]);

        if ($validated['is_default'] ?? false) {
            PriceList::where('is_default', true)->where('id', '!=', $priceList->id)->update(['is_default' => false]);
        }

        $priceList->update([
            'name' => $validated['name'],
            'is_default' => $validated['is_default'] ?? false,
        ]);

        if (!empty($validated['prices'])) {
            $sync = [];
            foreach ($validated['prices'] as $productId => $price) {
                if ($price !== null && $price !== '') {
                    $sync[$productId] = ['price' => $price];
                }
            }
            $priceList->products()->sync($sync);
        } else {
            $priceList->products()->sync([]);
        }

        toast('Lista de precio actualizada.', 'success');
        return redirect()->route('price-lists.index');
    }

    public function destroy(PriceList $priceList)
    {
        if ($priceList->is_default) {
            toast('No se puede eliminar la lista por defecto.', 'error');
            return redirect()->route('price-lists.index');
        }
        $priceList->delete();
        toast('Lista de precio eliminada.', 'success');
        return redirect()->route('price-lists.index');
    }
}
