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
        $this->call(CustomerGroupSeeder::class);

        $admin = User::firstOrCreate(
            ['email' => 'admin@pos.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('admin123'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('Admin');

        $branch = Branch::firstOrCreate(
            ['name' => 'Sucursal Principal'],
            ['address' => 'Av. Principal 123', 'phone' => '555-0100', 'email' => 'principal@pos.com', 'is_active' => true],
        );

        $warehouse = Warehouse::firstOrCreate(
            ['name' => 'Almacén Central'],
            ['description' => 'Almacén principal', 'address' => 'Av. Industrial 1500', 'phone' => '555-2000', 'is_active' => true],
        );

        foreach ([
            ['name' => 'Peso Cubano', 'code' => 'CUP', 'symbol' => '$', 'exchange_rate' => 1.0000, 'is_active' => true],
            ['name' => 'Dólar Estadounidense', 'code' => 'USD', 'symbol' => '$', 'exchange_rate' => 120.0000, 'is_active' => true],
            ['name' => 'Euro', 'code' => 'EUR', 'symbol' => '€', 'exchange_rate' => 130.0000, 'is_active' => true],
        ] as $currency) {
            Currency::firstOrCreate(['code' => $currency['code']], $currency);
        }

        CashRegister::firstOrCreate(
            ['name' => 'Caja Principal', 'branch_id' => $branch->id],
            ['is_active' => true],
        );
    }
}
