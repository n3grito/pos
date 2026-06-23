<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:role.view-any')->only('index');
        $this->middleware('can:role.create')->only(['create', 'store']);
        $this->middleware('can:role.update')->only(['edit', 'update']);
        $this->middleware('can:role.delete')->only('destroy');
    }

    public function index()
    {
        $roles = Role::withCount('permissions')->get();

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy(function ($p) {
            return explode('.', $p->name)[0];
        });

        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions']);

        session()->flash('success', 'Rol creado exitosamente.');
        return redirect()->route('roles.index');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy(function ($p) {
            return explode('.', $p->name)[0];
        });

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions']);

        session()->flash('success', 'Rol actualizado exitosamente.');
        return redirect()->route('roles.index');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'Admin') {
            session()->flash('error', 'No se puede eliminar el rol Admin.');
            return redirect()->route('roles.index');
        }

        $role->delete();

        session()->flash('success', 'Rol eliminado exitosamente.');
        return redirect()->route('roles.index');
    }
}
