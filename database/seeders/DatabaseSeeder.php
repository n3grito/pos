<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\CashRegister;
use App\Models\Currency;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PermissionSeeder::class);
        $this->call(UnitSeeder::class);
        $this->call(PriceListSeeder::class);

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@pos.com',
            'password' => bcrypt('admin123'),
        ]);
        $admin->assignRole('Admin');

        $branch = Branch::create([
            'name' => 'Sucursal Principal',
            'address' => 'Av. Principal 123',
            'phone' => '555-0100',
            'email' => 'principal@pos.com',
            'is_active' => true,
        ]);

        $warehouse = Warehouse::create([
            'name' => 'Almacén Central',
            'description' => 'Almacén principal',
            'address' => 'Av. Industrial 1500',
            'phone' => '555-2000',
            'is_active' => true,
        ]);

        Currency::insert([
            ['name' => 'Peso Cubano', 'code' => 'CUP', 'symbol' => '$', 'exchange_rate' => 1.0000, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dólar Estadounidense', 'code' => 'USD', 'symbol' => '$', 'exchange_rate' => 120.0000, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Euro', 'code' => 'EUR', 'symbol' => '€', 'exchange_rate' => 130.0000, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        CashRegister::create([
            'branch_id' => $branch->id,
            'name' => 'Caja Principal',
            'is_active' => true,
        ]);
    }
}
