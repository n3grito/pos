<?php

namespace App\Http\Controllers;

use App\Exports\ServicesExport;
use App\Imports\ServicesImport;
use App\Models\Category;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:service.view-any')->only('index');
        $this->middleware('can:service.view')->only('show');
        $this->middleware('can:service.create')->only(['create', 'store']);
        $this->middleware('can:service.update')->only(['edit', 'update']);
        $this->middleware('can:service.delete')->only('destroy');
        $this->middleware('can:service.export')->only('export');
        $this->middleware('can:service.import')->only('import');
    }

    public function index()
    {
        $services = Service::with('category')->latest()->paginate(10);
        return view('services.index', compact('services'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('services.create', compact('categories', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'selling_price' => 'required|numeric|min:0',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:0.001',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $service = Service::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'selling_price' => $validated['selling_price'],
            'tax_percentage' => $validated['tax_percentage'] ?? 0,
            'category_id' => $validated['category_id'] ?? null,
            'is_active' => $validated['is_active'],
        ]);

        $pivotData = [];
        foreach ($validated['products'] as $p) {
            $pivotData[$p['product_id']] = ['quantity' => $p['quantity']];
        }
        $service->products()->sync($pivotData);

        toast('Servicio creado correctamente.', 'success');
        return redirect()->route('services.index');
    }

    public function show(Service $service)
    {
        $service->load(['category', 'products']);
        return view('services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        $service->load('products');
        $categories = Category::where('is_active', true)->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('services.edit', compact('service', 'categories', 'products'));
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'selling_price' => 'required|numeric|min:0',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:0.001',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $service->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'selling_price' => $validated['selling_price'],
            'tax_percentage' => $validated['tax_percentage'] ?? 0,
            'category_id' => $validated['category_id'] ?? null,
            'is_active' => $validated['is_active'],
        ]);

        $pivotData = [];
        foreach ($validated['products'] as $p) {
            $pivotData[$p['product_id']] = ['quantity' => $p['quantity']];
        }
        $service->products()->sync($pivotData);

        toast('Servicio actualizado correctamente.', 'success');
        return redirect()->route('services.index');
    }

    public function export()
    {
        $export = new ServicesExport();
        $filePath = $export->export();

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $import = new ServicesImport();
        $result = $import->import($request->file('file')->path());

        $message = "{$result['imported']} servicios importados correctamente.";
        if (!empty($result['errors'])) {
            $message .= ' Errores: ' . implode(' | ', array_slice($result['errors'], 0, 5));
        }

        toast($message, 'success');
        return redirect()->route('services.index');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        toast('Servicio eliminado correctamente.', 'success');
        return redirect()->route('services.index');
    }
}
