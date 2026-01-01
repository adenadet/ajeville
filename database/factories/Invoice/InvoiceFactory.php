<?php

namespace Database\Factories\Invoice;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice\Invoice>
 */
class InvoiceFactory extends Factory
{
    
    public function definition(): array
    {
        return [
            'unique_id' => 'INV-'.sprintf('%08d', fake()->numberBetween(1, 10000)),
            'customer_id' => fake()->numberBetween(1, 10),
            'date' => fake()->date(),
            'due_date' => fake()->date(),
            'reference' => 'REF-'.rand(10, 3000),
            'tandc' => fake()->paragraph(2),
            'sub_total' =>  fake()->numberBetween(1000, 1000000),
            'discount' => fake()->numberBetween(1000, 10000),
            'logistics' => fake()->numberBetween(1000, 10000),
            'taxes' => fake()->numberBetween(1000, 10000),
        ];
    }
}
