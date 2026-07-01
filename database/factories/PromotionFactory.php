<?php

namespace Database\Factories;

use App\Models\Promotion;
use Illuminate\Database\Eloquent\Factories\Factory;

class PromotionFactory extends Factory
{
    protected $model = Promotion::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'type' => 'percentage',
            'value' => fake()->randomFloat(2, 5, 50),
            'min_amount' => 0,
            'min_quantity' => 0,
            'applies_to' => 'all',
            'start_date' => today(),
            'end_date' => today()->addDays(30),
            'is_active' => true,
        ];
    }
}
