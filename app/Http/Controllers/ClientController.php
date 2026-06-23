<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:client.view-any')->only('index');
        $this->middleware('can:client.view')->only('show');
        $this->middleware('can:client.create')->only(['create', 'store']);
        $this->middleware('can:client.update')->only(['edit', 'update']);
        $this->middleware('can:client.delete')->only('destroy');
    }

    public function index()
    {
        $clients = Client::paginate(10);

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'document_type' => 'nullable|string|max:20',
            'document_number' => 'nullable|string|max:20|unique:clients',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        Client::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Client created successfully.'], 201);
        }

        session()->flash('success', 'Client created successfully.');
        return redirect()->route('clients.index');
    }

    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'document_type' => 'nullable|string|max:20',
            'document_number' => 'nullable|string|max:20|unique:clients,document_number,' . $client->id,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $client->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Client updated successfully.']);
        }

        session()->flash('success', 'Client updated successfully.');
        return redirect()->route('clients.index');
    }

    public function destroy(Request $request, Client $client)
    {
        $client->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Client deleted successfully.']);
        }

        session()->flash('success', 'Client deleted successfully.');
        return redirect()->route('clients.index');
    }
}
