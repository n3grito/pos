<?php

namespace App\Http\Controllers;

use App\Models\CustomerGroup;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:promotion.view-any')->only('index');
        $this->middleware('can:promotion.view')->only('show');
        $this->middleware('can:promotion.create')->only(['create', 'store']);
        $this->middleware('can:promotion.update')->only(['edit', 'update']);
        $this->middleware('can:promotion.delete')->only('destroy');
    }

    public function index()
    {
        $promotions = Promotion::withCount('sales')->latest()->paginate(10);
        return view('promotions.index', compact('promotions'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get();
        $groups = CustomerGroup::all();
        return view('promotions.create', compact('products', 'groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed,bogo',
            'value' => 'required|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'min_quantity' => 'nullable|integer|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'applies_to' => 'required|in:all,products,groups',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'boolean',
            'usage_limit' => 'nullable|integer|min:0',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'group_ids' => 'nullable|array',
            'group_ids.*' => 'exists:customer_groups,id',
        ]);

        $promotion = Promotion::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'value' => $validated['value'],
            'min_amount' => $validated['min_amount'] ?? 0,
            'min_quantity' => $validated['min_quantity'] ?? 0,
            'max_discount' => $validated['max_discount'] ?? null,
            'applies_to' => $validated['applies_to'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $validated['is_active'] ?? true,
            'usage_limit' => $validated['usage_limit'] ?? null,
        ]);

        if ($validated['applies_to'] === 'products' && !empty($validated['product_ids'])) {
            $promotion->products()->sync($validated['product_ids']);
        }

        if ($validated['applies_to'] === 'groups' && !empty($validated['group_ids'])) {
            $promotion->customerGroups()->sync($validated['group_ids']);
        }

        toast('Promoción creada exitosamente.', 'success');
        return redirect()->route('promotions.index');
    }

    public function show(Promotion $promotion)
    {
        $promotion->load(['products', 'customerGroups', 'sales' => function ($q) {
            $q->latest()->limit(20);
        }]);
        return view('promotions.show', compact('promotion'));
    }

    public function edit(Promotion $promotion)
    {
        $products = Product::where('is_active', true)->get();
        $groups = CustomerGroup::all();
        $promotion->load(['products', 'customerGroups']);
        return view('promotions.edit', compact('promotion', 'products', 'groups'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed,bogo',
            'value' => 'required|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'min_quantity' => 'nullable|integer|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'applies_to' => 'required|in:all,products,groups',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'boolean',
            'usage_limit' => 'nullable|integer|min:0',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'group_ids' => 'nullable|array',
            'group_ids.*' => 'exists:customer_groups,id',
        ]);

        $promotion->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'value' => $validated['value'],
            'min_amount' => $validated['min_amount'] ?? 0,
            'min_quantity' => $validated['min_quantity'] ?? 0,
            'max_discount' => $validated['max_discount'] ?? null,
            'applies_to' => $validated['applies_to'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $validated['is_active'] ?? true,
            'usage_limit' => $validated['usage_limit'] ?? null,
        ]);

        if ($validated['applies_to'] === 'products') {
            $promotion->products()->sync($validated['product_ids'] ?? []);
        } else {
            $promotion->products()->sync([]);
        }

        if ($validated['applies_to'] === 'groups') {
            $promotion->customerGroups()->sync($validated['group_ids'] ?? []);
        } else {
            $promotion->customerGroups()->sync([]);
        }

        toast('Promoción actualizada exitosamente.', 'success');
        return redirect()->route('promotions.index');
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->products()->sync([]);
        $promotion->customerGroups()->sync([]);
        $promotion->delete();

        toast('Promoción eliminada exitosamente.', 'success');
        return redirect()->route('promotions.index');
    }

    public function toggleActive(Promotion $promotion)
    {
        $promotion->update(['is_active' => !$promotion->is_active]);

        return response()->json([
            'message' => 'Estado actualizado.',
            'is_active' => $promotion->is_active,
        ]);
    }
}
