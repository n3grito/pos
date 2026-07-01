<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;

class ManualController extends Controller
{
    private array $modules;
    private array $actionLabels;
    private array $moduleDescriptions;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:manual.view');

        $this->modules = [
            'sale' => 'Ventas',
            'purchase' => 'Compras',
            'product' => 'Productos',
            'service' => 'Servicios',
            'category' => 'Categorías',
            'client' => 'Clientes',
            'supplier' => 'Proveedores',
            'customer-group' => 'Grupos de Clientes',
            'promotion' => 'Promociones',
            'inventory' => 'Inventario',
            'warehouse' => 'Almacenes',
            'production' => 'Producción',
            'branch' => 'Sucursales',
            'cash-register' => 'Cajas Registradoras',
            'cash-register-session' => 'Sesiones de Caja',
            'report' => 'Reportes',
            'user' => 'Usuarios',
            'role' => 'Roles y Permisos',
            'currency' => 'Monedas',
            'setting' => 'Configuración',
            'manual' => 'Manuales',
            'activity-log' => 'Registro de Actividad',
            'database' => 'Base de Datos',
            'log' => 'Visor de Logs',
        ];

        $this->actionLabels = [
            'view-any' => 'Ver listado',
            'view' => 'Ver detalle',
            'create' => 'Crear',
            'update' => 'Editar',
            'delete' => 'Eliminar',
            'cancel' => 'Anular',
            'manage' => 'Gestionar',
            'open' => 'Abrir',
            'close' => 'Cerrar',
            'export' => 'Exportar',
            'import' => 'Importar',
            'stock' => 'Consultar stock',
            'transfer' => 'Transferir a inventario',
            'movements' => 'Ver movimientos',
            'adjustment' => 'Realizar ajustes',
            'low-stock' => 'Ver alertas de stock bajo',
            'complete' => 'Completar',
            'backup' => 'Respaldar',
            'explorer' => 'Explorar',
            'viewer' => 'Visualizar',
            'toggle-active' => 'Activar/Desactivar',
        ];

        $this->moduleDescriptions = [
            'sale' => 'Registra ventas, consulta el historial de transacciones, gestiona pedidos desde el kanban y anula ventas cuando sea necesario.',
            'purchase' => 'Administra las compras a proveedores: crea órdenes de compra, da seguimiento desde el kanban y consulta el historial.',
            'product' => 'Gestiona el catálogo de productos: crea, edita, importa y exporta productos con sus precios y existencias.',
            'service' => 'Administra los servicios ofrecidos: crea, edita, importa y exporta servicios.',
            'category' => 'Organiza productos y servicios en categorías para facilitar su búsqueda y clasificación.',
            'client' => 'Registra y administra la información de los clientes, consulta su historial de compras y puntos de fidelidad.',
            'supplier' => 'Gestiona los proveedores: datos de contacto, condiciones y seguimiento de compras.',
            'customer-group' => 'Crea y administra grupos de clientes con reglas de descuento y acumulación de puntos.',
            'promotion' => 'Diseña promociones y descuentos especiales (porcentaje, monto fijo o 2x1) para aplicar en ventas.',
            'inventory' => 'Controla el stock general, consulta movimientos, realiza ajustes de inventario y recibe alertas de stock bajo.',
            'warehouse' => 'Gestiona los almacenes físicos y virtuales, consulta el stock por almacén y realiza transferencias.',
            'production' => 'Gestiona órdenes de producción, da seguimiento al proceso y registra la producción completada.',
            'branch' => 'Administra las sucursales o puntos de venta del negocio.',
            'cash-register' => 'Configura y administra las cajas registradoras disponibles en cada sucursal.',
            'cash-register-session' => 'Abre y cierra sesiones de caja registradora, consulta el historial de sesiones.',
            'report' => 'Genera reportes de ventas, productos más vendidos y alertas de stock bajo con filtros combinados.',
            'user' => 'Administra los usuarios del sistema: crea, edita y desactiva cuentas de usuario.',
            'role' => 'Gestiona los roles y asigna permisos a cada rol para controlar el acceso a las funcionalidades.',
            'currency' => 'Configura las monedas utilizadas en el sistema y define la moneda por defecto.',
            'setting' => 'Configura los parámetros generales del sistema, diseño del recibo y configuración de correo.',
            'manual' => 'Consulta este manual de usuario para conocer las funcionalidades disponibles según tu rol.',
            'activity-log' => 'Visualiza el registro detallado de todas las actividades realizadas en el sistema.',
            'database' => 'Realiza copias de seguridad de la base de datos y explora su contenido.',
            'log' => 'Visualiza los registros técnicos del sistema para depuración y monitoreo.',
        ];
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('Admin')) {
            $roles = Role::with('permissions')->where('name', '!=', 'Admin')->get();
            $adminRole = Role::where('name', 'Admin')->with('permissions')->first();
        } else {
            $roles = collect([$user->roles->first()])->filter();
            $adminRole = null;
        }

        return view('manuals.index', compact('roles', 'adminRole'));
    }

    public function show(Role $role)
    {
        $user = auth()->user();

        if (!$user->hasRole('Admin') && !$user->hasRole($role->name)) {
            abort(403);
        }

        $role->load('permissions');
        $permissions = $role->permissions;
        $grouped = $this->groupPermissions($permissions);

        $isCurrentUserRole = $user->hasRole($role->name);

        return view('manuals.show', compact('role', 'grouped', 'isCurrentUserRole'));
    }

    private function groupPermissions($permissions): array
    {
        $grouped = [];

        foreach ($permissions as $perm) {
            $parts = explode('.', $perm->name);
            $moduleKey = $parts[0];
            $action = $parts[1] ?? '';

            $moduleName = $this->modules[$moduleKey] ?? ucfirst($moduleKey);
            $actionName = $this->actionLabels[$action] ?? $action;
            $description = $this->moduleDescriptions[$moduleKey] ?? '';

            if (!isset($grouped[$moduleKey])) {
                $grouped[$moduleKey] = [
                    'name' => $moduleName,
                    'description' => $description,
                    'actions' => [],
                ];
            }
            $grouped[$moduleKey]['actions'][] = $actionName;
        }

        ksort($grouped);

        return $grouped;
    }
}
