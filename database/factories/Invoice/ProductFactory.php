<?php

namespace Database\Factories\Invoice;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'item_code' => 'IC-1000'.rand(10, 500),
            'description' => 'Name of Product '.rand(10, 500),
            'unit_price' => mt_rand(100, 1000)
        ];
    }
}
