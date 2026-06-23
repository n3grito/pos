<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:branch.view-any')->only('index');
        $this->middleware('can:branch.view')->only('show');
        $this->middleware('can:branch.create')->only(['create', 'store']);
        $this->middleware('can:branch.update')->only(['edit', 'update']);
        $this->middleware('can:branch.delete')->only('destroy');
    }

    public function index()
    {
        $branches = Branch::paginate(10);

        return view('branches.index', compact('branches'));
    }

    public function create()
    {
        return view('branches.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        Branch::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Branch created successfully.'], 201);
        }

        session()->flash('success', 'Branch created successfully.');
        return redirect()->route('branches.index');
    }

    public function show(Branch $branch)
    {
        return view('branches.show', compact('branch'));
    }

    public function edit(Branch $branch)
    {
        return view('branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $branch->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Branch updated successfully.']);
        }

        session()->flash('success', 'Branch updated successfully.');
        return redirect()->route('branches.index');
    }

    public function destroy(Request $request, Branch $branch)
    {
        $branch->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Branch deleted successfully.']);
        }

        session()->flash('success', 'Branch deleted successfully.');
        return redirect()->route('branches.index');
    }
}
