<?php

namespace Database\Factories\Invoice;

use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'invoice_id' => fake()->numberBetween(1, 10),
            'product_id' => fake()->numberBetween(1, 10),
            'unit_price' => fake()->numberBetween(1000, 10000),
            'quantity' => rand(1, 10),
            'discount' => rand(0, 200),
        ];
    }
}
