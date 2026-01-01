<?php

namespace Database\Factories\Invoice;

use Illuminate\Database\Eloquent\Factories\Factory;

class CounterFactory extends Factory
{
    public function definition(): array
    {
        return [
            'key' => 'invoice',
            'prefix' => 'INV-',
            'value' => 20000,
        ];
    }
}
