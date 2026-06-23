<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:user.view-any')->only('index');
        $this->middleware('can:user.view')->only('show');
        $this->middleware('can:user.create')->only(['create', 'store']);
        $this->middleware('can:user.update')->only(['edit', 'update']);
        $this->middleware('can:user.delete')->only('destroy');
    }

    public function index()
    {
        $users = User::with('branch', 'roles')->paginate(10);
        $branches = Branch::all();
        $roles = Role::all();

        return view('users.index', compact('users', 'branches', 'roles'));
    }

    public function create()
    {
        $branches = Branch::all();
        $roles = Role::all();
        $warehouses = Warehouse::where('is_active', true)->get();

        return view('users.create', compact('branches', 'roles', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'branch_id' => 'nullable|exists:branches,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'branch_id' => $validated['branch_id'],
            'warehouse_id' => $validated['warehouse_id'],
            'is_active' => $request->boolean('is_active'),
        ]);

        $user->syncRoles($validated['roles']);

        session()->flash('success', 'Usuario creado exitosamente.');
        return redirect()->route('users.index');
    }

    public function show(User $user)
    {
        $user->load('branch', 'roles');

        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $user->load('roles');
        $branches = Branch::all();
        $roles = Role::all();
        $warehouses = Warehouse::where('is_active', true)->get();

        return view('users.edit', compact('user', 'branches', 'roles', 'warehouses'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'branch_id' => 'nullable|exists:branches,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'is_active' => 'boolean',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->branch_id = $validated['branch_id'];
        $user->warehouse_id = $validated['warehouse_id'];
        $user->is_active = $request->boolean('is_active');

        if ($validated['password']) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        $user->syncRoles($validated['roles']);

        session()->flash('success', 'Usuario actualizado exitosamente.');
        return redirect()->route('users.index');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            session()->flash('error', 'No puedes eliminar tu propio usuario.');
            return redirect()->route('users.index');
        }

        $user->delete();

        session()->flash('success', 'Usuario eliminado exitosamente.');
        return redirect()->route('users.index');
    }
}
