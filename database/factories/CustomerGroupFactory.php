<?php

namespace Database\Factories;

use App\Models\CustomerGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerGroupFactory extends Factory
{
    protected $model = CustomerGroup::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'discount_percentage' => fake()->randomFloat(2, 0, 20),
            'color' => fake()->hexColor(),
            'is_default' => false,
        ];
    }
}
