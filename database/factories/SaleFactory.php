<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        return [
            'invoice_number' => 'INV-' . date('Ymd') . '-' . str_pad(fake()->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'user_id' => User::factory(),
            'branch_id' => Branch::factory(),
            'subtotal' => 0,
            'tax' => 0,
            'total' => fake()->randomFloat(2, 50, 5000),
            'payment_method' => 'cash',
            'status' => 'completed',
            'date' => today(),
        ];
    }
}
