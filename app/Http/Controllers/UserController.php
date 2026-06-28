<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

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
        $permissions = Permission::all()->groupBy(fn ($p) => explode('.', $p->name)[0]);

        return view('users.create', compact('branches', 'roles', 'warehouses', 'permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'nit' => 'nullable|string|size:11|regex:/^\d{11}$/|unique:users,nit',
            'address' => 'nullable|string|max:500',
            'phone_personal' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
            'branch_id' => 'nullable|exists:branches,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nit' => $validated['nit'] ?? null,
            'address' => $validated['address'] ?? null,
            'phone_personal' => $validated['phone_personal'] ?? null,
            'password' => Hash::make($validated['password']),
            'branch_id' => $validated['branch_id'],
            'warehouse_id' => $validated['warehouse_id'],
            'is_active' => $request->boolean('is_active'),
            'must_change_password' => true,
        ]);

        $roles = Role::whereIn('id', $validated['roles'] ?? [])->pluck('name')->toArray();
        $user->syncRoles($roles);
        $user->syncPermissions($validated['permissions'] ?? []);

        toast('Usuario creado exitosamente.', 'success');
        return redirect()->route('users.index');
    }

    public function show(User $user)
    {
        $user->load('branch', 'roles');

        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $user->load('roles', 'permissions');
        $branches = Branch::all();
        $roles = Role::all();
        $warehouses = Warehouse::where('is_active', true)->get();
        $permissions = Permission::all()->groupBy(fn ($p) => explode('.', $p->name)[0]);

        return view('users.edit', compact('user', 'branches', 'roles', 'warehouses', 'permissions'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'nit' => ['nullable', 'string', 'size:11', 'regex:/^\d{11}$/', Rule::unique('users', 'nit')->ignore($user->id)],
            'address' => 'nullable|string|max:500',
            'phone_personal' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
            'branch_id' => 'nullable|exists:branches,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'is_active' => 'boolean',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->nit = $validated['nit'] ?? null;
        $user->address = $validated['address'] ?? null;
        $user->phone_personal = $validated['phone_personal'] ?? null;
        $user->branch_id = $validated['branch_id'];
        $user->warehouse_id = $validated['warehouse_id'];
        $user->is_active = $request->boolean('is_active');

        if ($validated['password']) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        $roles = Role::whereIn('id', $validated['roles'] ?? [])->pluck('name')->toArray();
        $user->syncRoles($roles);
        $user->syncPermissions($validated['permissions'] ?? []);

        toast('Usuario actualizado exitosamente.', 'success');
        return redirect()->route('users.index');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            toast('No puedes eliminar tu propio usuario.', 'error', true);
            return redirect()->route('users.index');
        }

        $user->delete();

        toast('Usuario eliminado exitosamente.', 'success');
        return redirect()->route('users.index');
    }
}
