<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $modules = [
            'branch', 'category', 'product', 'client', 'supplier',
            'purchase', 'sale', 'cash-register', 'report', 'user', 'role',
            'warehouse', 'currency', 'service',
        ];

        $permissions = [];

        foreach ($modules as $module) {
            $permissions[] = "{$module}.view-any";
            $permissions[] = "{$module}.view";
            $permissions[] = "{$module}.create";
            $permissions[] = "{$module}.update";
            $permissions[] = "{$module}.delete";
        }

        $extra = [
            'sale.cancel',
            'purchase.cancel',
            'setting.manage',
            'warehouse.stock',
            'warehouse.transfer',
            'inventory.view',
            'inventory.movements',
            'inventory.adjustment',
            'inventory.low-stock',
            'cash-register-session.open',
            'cash-register-session.close',
            'cash-register-session.view-any',
        ];

        $permissions = array_merge($permissions, $extra);

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin->syncPermissions($permissions);

        $manager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $manager->syncPermissions(array_filter($permissions, fn ($p) => !str_ends_with($p, '.delete')));

        $cashierPermissions = [
            'sale.view-any', 'sale.view', 'sale.create', 'sale.cancel',
            'cash-register-session.open', 'cash-register-session.close',
            'cash-register-session.view-any',
            'inventory.view',
        ];
        $cashier = Role::firstOrCreate(['name' => 'Cashier', 'guard_name' => 'web']);
        $cashier->syncPermissions($cashierPermissions);
    }
}
