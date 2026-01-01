<?php

namespace Database\Factories\Escrows;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Escrows\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => fake()->dateTimeThisYear(),
            'buyer_id' => rand(1, 10),
            'seller_id' => rand(1, 10),
            'invoice_id' => rand(1, 20),
            'item_details' => fake()->paragraph(3),
            'item_type_id' => rand(1, 5),
            'amount' => mt_rand(11000, 100000),
            'status_id' => 0,
            'created_by' => rand(1, 10),
            'updated_by' => rand(1, 10),
        ];
    }
}
