<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Unidad', 'abbreviation' => 'Ud', 'is_active' => true],
            ['name' => 'Pieza', 'abbreviation' => 'Pz', 'is_active' => true],
            ['name' => 'Kilogramo', 'abbreviation' => 'Kg', 'is_active' => true],
            ['name' => 'Gramo', 'abbreviation' => 'g', 'is_active' => true],
            ['name' => 'Litro', 'abbreviation' => 'L', 'is_active' => true],
            ['name' => 'Mililitro', 'abbreviation' => 'mL', 'is_active' => true],
            ['name' => 'Caja', 'abbreviation' => 'Cj', 'is_active' => true],
            ['name' => 'Paquete', 'abbreviation' => 'Pq', 'is_active' => true],
            ['name' => 'Metro', 'abbreviation' => 'm', 'is_active' => true],
            ['name' => 'Docena', 'abbreviation' => 'Doc', 'is_active' => true],
            ['name' => 'Galón', 'abbreviation' => 'Gal', 'is_active' => true],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(['name' => $unit['name']], $unit);
        }
    }
}
