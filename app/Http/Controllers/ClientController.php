<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CustomerGroup;
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
        $clients = Client::latest()->paginate(10);
        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        $customerGroups = CustomerGroup::all();
        return view('clients.create', compact('customerGroups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client_type' => 'required|string|in:' . implode(',', array_keys(Client::TYPES)),
            'document_number' => 'required|string|max:30|unique:clients,document_number',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'customer_group_id' => 'nullable|exists:customer_groups,id',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('clients', 'public');
        }

        Client::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Client created successfully.'], 201);
        }

        toast('Cliente creado exitosamente.', 'success');
        return redirect()->route('clients.index');
    }

    public function show(Client $client)
    {
        $client->load(['customerGroup', 'sales' => fn($q) => $q->latest()->limit(50)]);
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        $customerGroups = CustomerGroup::all();
        return view('clients.edit', compact('client', 'customerGroups'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client_type' => 'required|string|in:' . implode(',', array_keys(Client::TYPES)),
            'document_number' => 'required|string|max:30|unique:clients,document_number,' . $client->id,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'customer_group_id' => 'nullable|exists:customer_groups,id',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($client->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($client->photo);
            }
            $validated['photo'] = $request->file('photo')->store('clients', 'public');
        } elseif ($request->boolean('remove_photo') && $client->photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($client->photo);
            $validated['photo'] = null;
        }

        $client->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Client updated successfully.']);
        }

        toast('Cliente actualizado exitosamente.', 'success');
        return redirect()->route('clients.index');
    }

    public function destroy(Request $request, Client $client)
    {
        if ($client->photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($client->photo);
        }
        $client->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Client deleted successfully.']);
        }

        toast('Cliente eliminado exitosamente.', 'success');
        return redirect()->route('clients.index');
    }
}
