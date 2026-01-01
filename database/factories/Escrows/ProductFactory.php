<?php

namespace Database\Factories\Escrows;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{

    public function definition(): array
    {
        return [
            'item_code'     => 'IC-1000'.rand(10, 500),
            'description'   => 'Name of Product '.rand(10, 5000),
            'unit_price'    => mt_rand(100, 1000),
            'details'       => fake()->paragraph(3),
            'status'        => 1,
            'quantity'      => mt_rand(1, 1000),
            'created_by'    => mt_rand(1, 10),
            'updated_by'    => mt_rand(1, 10),
        ];
    }
}
