<?php

namespace App\Http\Controllers;

use App\Exports\ProductsExport;
use App\Imports\ProductsImport;
use App\Models\Product;
use App\Models\Category;
use App\Models\Branch;
use App\Models\Unit;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:product.view-any')->only('index');
        $this->middleware('can:product.view')->only('show');
        $this->middleware('can:product.create')->only(['create', 'store']);
        $this->middleware('can:product.update')->only(['edit', 'update']);
        $this->middleware('can:product.delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = Product::with(['category', 'branch', 'unit']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($status = $request->get('status')) {
            $query->where('is_active', $status === 'active');
        }

        if ($request->has('available_for_sale') && $request->get('available_for_sale') !== '') {
            $query->where('available_for_sale', $request->boolean('available_for_sale'));
        }

        $products = $query->paginate(10);

        $categories = Category::where('is_active', true)->get();
        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $branches = Branch::where('is_active', true)->get();
        $units = Unit::where('is_active', true)->get();

        return view('products.create', compact('categories', 'branches', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:50|unique:products',
            'barcode' => 'nullable|string|max:50|unique:products',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'branch_id' => 'required|exists:branches,id',
            'unit_id' => 'nullable|exists:units,id',
            'cost_price' => 'nullable|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'stock' => 'nullable|numeric|min:0',
            'min_stock' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'available_for_sale' => 'boolean',
        ]);

        $validated['tax_percentage'] = $validated['tax_percentage'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active');
        $validated['available_for_sale'] = $request->boolean('available_for_sale');
        Product::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Product created successfully.'], 201);
        }

        session()->flash('success', 'Product created successfully.');
        return redirect()->route('products.index');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'branch', 'unit', 'inventoryMovements.user']);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        $branches = Branch::where('is_active', true)->get();
        $units = Unit::where('is_active', true)->get();

        return view('products.edit', compact('product', 'categories', 'branches', 'units'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:50|unique:products,barcode,' . $product->id,
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'branch_id' => 'required|exists:branches,id',
            'unit_id' => 'nullable|exists:units,id',
            'cost_price' => 'nullable|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'stock' => 'nullable|numeric|min:0',
            'min_stock' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'available_for_sale' => 'boolean',
        ]);

        $validated['tax_percentage'] = $validated['tax_percentage'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active');
        $validated['available_for_sale'] = $request->boolean('available_for_sale');
        $product->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Product updated successfully.']);
        }

        session()->flash('success', 'Product updated successfully.');
        return redirect()->route('products.index');
    }

    public function export()
    {
        $export = new ProductsExport();
        $filePath = $export->export();

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $import = new ProductsImport();
        $result = $import->import($request->file('file')->path());

        $message = "{$result['imported']} productos importados correctamente.";
        if (!empty($result['errors'])) {
            $message .= ' Errores: ' . implode(' | ', array_slice($result['errors'], 0, 5));
        }

        session()->flash('success', $message);
        return redirect()->route('products.index');
    }

    public function destroy(Request $request, Product $product)
    {
        $product->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Product deleted successfully.']);
        }

        session()->flash('success', 'Product deleted successfully.');
        return redirect()->route('products.index');
    }
}
