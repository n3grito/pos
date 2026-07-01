<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'branch_id' => Branch::factory(),
            'name' => fake()->unique()->words(2, true),
            'sku' => strtoupper(fake()->bothify('???-###')),
            'barcode' => fake()->unique()->ean13(),
            'selling_price' => fake()->randomFloat(2, 10, 500),
            'cost_price' => fake()->randomFloat(2, 5, 200),
            'tax_percentage' => 0,
            'stock' => fake()->numberBetween(10, 100),
            'min_stock' => 5,
            'is_active' => true,
            'available_for_sale' => true,
        ];
    }
}
