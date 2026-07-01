<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDashboardWidget extends Model
{
    protected $fillable = ['user_id', 'widget_key', 'enabled', 'order', 'settings'];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'order' => 'integer',
            'settings' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getDefaultWidgets(): array
    {
        return [
            'sales-kpi' => ['label' => 'Ventas KPI', 'description' => 'Total de ventas, ingresos y métricas del día', 'icon' => 'chart-bar'],
            'low-stock' => ['label' => 'Stock Bajo', 'description' => 'Productos con inventario crítico', 'icon' => 'exclamation-triangle'],
            'recent-sales' => ['label' => 'Ventas Recientes', 'description' => 'Últimas 10 transacciones', 'icon' => 'receipt'],
            'top-products' => ['label' => 'Top Productos', 'description' => 'Productos más vendidos del día', 'icon' => 'star'],
            'recent-activity' => ['label' => 'Actividad Reciente', 'description' => 'Registro de actividad del sistema', 'icon' => 'activity'],
            'quick-actions' => ['label' => 'Acciones Rápidas', 'description' => 'Atajos a funcionalidades comunes', 'icon' => 'zap'],
            'cash-registers' => ['label' => 'Cajas', 'description' => 'Estado de las cajas registradoras', 'icon' => 'dollar-sign'],
            'sales-chart' => ['label' => 'Gráfico de Ventas', 'description' => 'Gráfico semanal de ventas', 'icon' => 'trending-up'],
        ];
    }

    public static function seedForUser(int $userId): void
    {
        $order = 0;
        foreach (self::getDefaultWidgets() as $key => $config) {
            self::create([
                'user_id' => $userId,
                'widget_key' => $key,
                'enabled' => !in_array($key, ['recent-activity', 'top-products']),
                'order' => $order++,
            ]);
        }
    }
}
