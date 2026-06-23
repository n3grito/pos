<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;

class ManualController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $roles = Role::with('permissions')->where('name', '!=', 'Admin')->get();

        $adminRole = Role::where('name', 'Admin')->with('permissions')->first();

        if (!auth()->user()->hasRole('Admin')) {
            $roles = collect([auth()->user()->roles->first()])->filter();
        }

        return view('manuals.index', compact('roles', 'adminRole'));
    }

    public function show(Role $role)
    {
        $role->load('permissions');

        if (!auth()->user()->hasRole('Admin') && !auth()->user()->hasRole($role->name)) {
            abort(403);
        }

        $permissions = $role->permissions;
        $grouped = $this->groupPermissions($permissions);

        return view('manuals.show', compact('role', 'grouped'));
    }

    private function groupPermissions($permissions): array
    {
        $modules = [
            'sale' => 'Ventas',
            'purchase' => 'Compras',
            'product' => 'Productos',
            'service' => 'Servicios',
            'category' => 'Categorías',
            'client' => 'Clientes',
            'supplier' => 'Proveedores',
            'inventory' => 'Inventario',
            'warehouse' => 'Almacenes',
            'branch' => 'Sucursales',
            'cash-register' => 'Cajas Registradoras',
            'cash-register-session' => 'Sesiones de Caja',
            'report' => 'Reportes',
            'user' => 'Usuarios',
            'role' => 'Roles y Permisos',
            'currency' => 'Monedas',
            'setting' => 'Configuración',
        ];

        $actionLabels = [
            'view-any' => 'Ver listado',
            'view' => 'Ver detalle',
            'create' => 'Crear',
            'update' => 'Editar',
            'delete' => 'Eliminar',
            'cancel' => 'Anular',
            'manage' => 'Gestionar',
            'open' => 'Abrir',
            'close' => 'Cerrar',
            'stock' => 'Consultar stock',
            'transfer' => 'Transferir a inventario',
            'movements' => 'Ver movimientos',
            'adjustment' => 'Realizar ajustes',
            'low-stock' => 'Ver alertas de stock bajo',
        ];

        $grouped = [];

        foreach ($permissions as $perm) {
            $parts = explode('.', $perm->name);
            $module = $parts[0];
            $action = $parts[1] ?? '';

            $moduleName = $modules[$module] ?? ucfirst($module);
            $actionName = $actionLabels[$action] ?? $action;

            if (!isset($grouped[$moduleName])) {
                $grouped[$moduleName] = [];
            }
            $grouped[$moduleName][] = $actionName;
        }

        return $grouped;
    }
}
