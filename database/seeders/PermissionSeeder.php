<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        try {
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        } catch (\Exception $e) {
            // Redis may not be available; proceed anyway
        }

        // Standard CRUD modules (view-any, view, create, update, delete)
        $crudModules = [
            'branch', 'category', 'product', 'client', 'supplier',
            'purchase', 'cash-register', 'user', 'warehouse', 'currency', 'service', 'production',
        ];

        // sale (no delete), role (no view)
        $saleModule = ['sale.view-any', 'sale.view', 'sale.create', 'sale.cancel'];
        $roleModule = ['role.view-any', 'role.create', 'role.update', 'role.delete'];

        $permissions = [...$saleModule, ...$roleModule];

        foreach ($crudModules as $module) {
            $permissions[] = "{$module}.view-any";
            $permissions[] = "{$module}.view";
            $permissions[] = "{$module}.create";
            $permissions[] = "{$module}.update";
            $permissions[] = "{$module}.delete";
        }

        // Module-specific extra permissions
        $extra = [
            'purchase.cancel',
            'product.export',
            'product.import',
            'service.export',
            'service.import',
            'currency.toggle-active',
            'setting.manage',
            'warehouse.stock',
            'warehouse.transfer',
            'production.complete',
            'production.cancel',
            'inventory.view',
            'inventory.movements',
            'inventory.adjustment',
            'inventory.low-stock',
            'cash-register-session.open',
            'cash-register-session.close',
            'cash-register-session.view-any',
            'activity-log.view',
            'database.backup',
            'database.explorer',
            'log.viewer',
            'manual.view',
            'report.view-any',
        ];

        // CRM module permissions
        $crmModules = ['promotion', 'customer-group'];
        foreach ($crmModules as $module) {
            $permissions[] = "{$module}.view-any";
            $permissions[] = "{$module}.view";
            $permissions[] = "{$module}.create";
            $permissions[] = "{$module}.update";
            $permissions[] = "{$module}.delete";
        }

        $permissions = array_merge($permissions, $extra);

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Remove stale permissions from previous versions
        $stale = ['sale.update', 'sale.delete', 'report.view', 'report.create', 'report.update', 'report.delete', 'role.view'];
        Permission::whereIn('name', $stale)->delete();

        // --- Role assignments ---

        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin->syncPermissions($permissions);

        // Manager: all except .delete, database.*, log.*
        $managerPermissions = array_filter($permissions, fn ($p) =>
            !str_ends_with($p, '.delete')
            && $p !== 'database.backup'
            && $p !== 'database.explorer'
            && $p !== 'log.viewer'
        );
        $manager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $manager->syncPermissions($managerPermissions);

        // Cashier: sales + cash register + inventory view
        $cashierPermissions = [
            'sale.view-any', 'sale.view', 'sale.create', 'sale.cancel',
            'cash-register-session.open', 'cash-register-session.close',
            'cash-register-session.view-any',
            'inventory.view',
            'manual.view',
        ];
        $cashier = Role::firstOrCreate(['name' => 'Cashier', 'guard_name' => 'web']);
        $cashier->syncPermissions($cashierPermissions);
    }
}
