<?php

namespace Database\Seeders;

use App\Models\CustomerGroup;
use Illuminate\Database\Seeder;

class CustomerGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            ['name' => 'General', 'discount_percentage' => 0, 'color' => '#6366f1', 'is_default' => true],
            ['name' => 'VIP', 'discount_percentage' => 10, 'color' => '#f59e0b', 'is_default' => false],
            ['name' => 'Mayorista', 'discount_percentage' => 15, 'color' => '#10b981', 'is_default' => false],
            ['name' => 'Estudiante', 'discount_percentage' => 5, 'color' => '#3b82f6', 'is_default' => false],
        ];

        foreach ($groups as $group) {
            CustomerGroup::firstOrCreate(['name' => $group['name']], $group);
        }
    }
}
