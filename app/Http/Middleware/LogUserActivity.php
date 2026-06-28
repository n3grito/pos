<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    protected array $skipRoutes = [
        'activity-logs.stream',
        'csp.report',
        'dashboard.chart-data',
    ];

    protected array $moduleMap = [
        'branches' => 'Sucursales',
        'categories' => 'Categorías',
        'products' => 'Productos',
        'services' => 'Servicios',
        'clients' => 'Clientes',
        'suppliers' => 'Proveedores',
        'purchases' => 'Compras',
        'sales' => 'Ventas',
        'price-lists' => 'Listas de Precios',
        'cash-registers' => 'Cajas',
        'cash-register-sessions' => 'Sesiones de Caja',
        'users' => 'Usuarios',
        'roles' => 'Roles',
        'currencies' => 'Monedas',
        'inventory' => 'Inventario',
        'warehouses' => 'Almacenes',
        'reports' => 'Reportes',
        'settings' => 'Configuración',
        'database' => 'Base de Datos',
        'activity-logs' => 'Registro de Actividad',
        'logs' => 'Logs',
        'dashboard' => 'Dashboard',
        'profile' => 'Perfil',
        'manuals' => 'Manuales',
        'register' => 'Registro',
        'password' => 'Contraseña',
        'verification' => 'Verificación',
    ];

    protected array $actionMap = [
        'index' => 'Listar',
        'create' => 'Crear',
        'store' => 'Crear',
        'show' => 'Ver',
        'edit' => 'Editar',
        'update' => 'Actualizar',
        'destroy' => 'Eliminar',
        'import' => 'Importar',
        'export' => 'Exportar',
        'cancel' => 'Cancelar',
        'open' => 'Abrir',
        'close' => 'Cerrar',
        'toggle-active' => 'Activar/Desactivar',
        'toggle-availability' => 'Cambiar Disponibilidad',
        'movements' => 'Ver Movimientos',
        'adjustment' => 'Ajustar Stock',
        'adjustment.store' => 'Ajustar Stock',
        'low-stock' => 'Ver Stock Bajo',
        'transfer' => 'Transferir',
        'transfer.store' => 'Transferir',
        'stock' => 'Ver Stock',
        'products' => 'Ver Productos',
        'sales' => 'Reporte de Ventas',
        'top-products' => 'Productos Top',
        'low-stock' => 'Stock Bajo',
        'mail' => 'Configurar Correo',
        'mail.update' => 'Actualizar Correo',
        'mail.test' => 'Probar Correo',
        'receipt' => 'Configurar Comprobante',
        'receipt.update' => 'Actualizar Comprobante',
        'general' => 'Configurar General',
        'general.update' => 'Actualizar General',
        'backups' => 'Ver Respaldos',
        'backups.create' => 'Crear Respaldo',
        'backups.download' => 'Descargar Respaldo',
        'backups.destroy' => 'Eliminar Respaldo',
        'backups.restore' => 'Restaurar Respaldo',
        'explorer.index' => 'Explorar Base de Datos',
        'explorer.show' => 'Ver Tabla',
        'explorer.query' => 'Ejecutar Consulta',
        'kanban' => 'Kanban',
        'chart-data' => 'Ver Gráficos',
        'edit' => 'Editar Perfil',
        'update' => 'Actualizar Perfil',
        'destroy' => 'Eliminar Cuenta',
        'toggle-active' => 'Activar/Desactivar',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!auth()->check()) {
            return $response;
        }

        $route = $request->route();
        if (!$route) {
            return $response;
        }

        $routeName = $route->getName();
        $method = $request->method();

        if (!$routeName || in_array($routeName, $this->skipRoutes, true)) {
            return $response;
        }

        $action = $this->resolveAction($routeName, $method);
        $module = $this->resolveModule($routeName);
        $description = $this->buildDescription($module, $action, $routeName, $request);
        $severity = $this->resolveSeverity($method, $routeName);
        $notable = $this->isNotable($method, $routeName);
        $modelInfo = $this->extractModelInfo($route);

        if ($description) {
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => $routeName,
                'severity' => $severity,
                'notable' => $notable,
                'description' => $description,
                'model_type' => $modelInfo['type'],
                'model_id' => $modelInfo['id'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return $response;
    }

    protected function resolveAction(string $routeName, string $method): string
    {
        $parts = explode('.', $routeName);
        $actionKey = count($parts) >= 2 ? $parts[count($parts) - 1] : $routeName;

        if (isset($this->actionMap[$routeName])) {
            return $this->actionMap[$routeName];
        }

        return $this->actionMap[$actionKey] ?? $actionKey;
    }

    protected function resolveModule(string $routeName): string
    {
        $parts = explode('.', $routeName);
        $moduleKey = $parts[0] ?? $routeName;

        return $this->moduleMap[$moduleKey] ?? ucfirst($moduleKey);
    }

    protected function buildDescription(string $module, string $action, string $routeName, Request $request): string
    {
        $modelInfo = $this->extractModelInfo($request->route());
        $identifier = '';

        if ($modelInfo['id']) {
            $identifier = " #{$modelInfo['id']}";
        }

        $text = "{$module} → {$action}{$identifier}";

        return $text;
    }

    protected function resolveSeverity(string $method, string $routeName): string
    {
        if (in_array($routeName, ['login', 'logout', 'login_failed'], true)) {
            return 'info';
        }

        return match ($method) {
            'DELETE' => 'critical',
            'POST', 'PUT', 'PATCH' => 'warning',
            default => 'info',
        };
    }

    protected function isNotable(string $method, string $routeName): bool
    {
        if (in_array($routeName, ['login', 'logout'], true)) {
            return false;
        }

        if ($method === 'DELETE') {
            return true;
        }

        if (str_contains($routeName, '.store') || str_contains($routeName, '.create')) {
            return true;
        }

        return false;
    }

    protected function extractModelInfo($route): array
    {
        $params = $route->parameters();

        $modelParameterNames = [
            'branch', 'category', 'product', 'service', 'client', 'supplier',
            'purchase', 'sale', 'cash_register', 'cashRegister',
            'cash_register_session', 'cashRegisterSession',
            'user', 'role', 'currency', 'warehouse', 'price_list', 'priceList',
        ];

        foreach ($modelParameterNames as $param) {
            if (isset($params[$param])) {
                $model = $params[$param];
                if (is_object($model)) {
                    return [
                        'type' => get_class($model),
                        'id' => $model->id,
                    ];
                }
                return [
                    'type' => null,
                    'id' => $model,
                ];
            }
        }

        foreach ($params as $key => $value) {
            if (is_object($value) && method_exists($value, 'getKey')) {
                return [
                    'type' => get_class($value),
                    'id' => $value->getKey(),
                ];
            }
        }

        return ['type' => null, 'id' => null];
    }
}
