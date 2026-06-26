<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:category.view-any')->only('index');
        $this->middleware('can:category.view')->only('show');
        $this->middleware('can:category.create')->only(['create', 'store']);
        $this->middleware('can:category.update')->only(['edit', 'update']);
        $this->middleware('can:category.delete')->only('destroy');
    }

    public function index()
    {
        $categories = Category::paginate(10);

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Category::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Category created successfully.'], 201);
        }

        toast('Category created successfully.', 'success');
        return redirect()->route('categories.index');
    }

    public function show(Category $category)
    {
        return view('categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Category updated successfully.']);
        }

        toast('Category updated successfully.', 'success');
        return redirect()->route('categories.index');
    }

    public function destroy(Request $request, Category $category)
    {
        $category->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Category deleted successfully.']);
        }

        toast('Category deleted successfully.', 'success');
        return redirect()->route('categories.index');
    }
}
