<?php

namespace Tests\Feature\Sales;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderTest extends TestCase
{
    public function test_sales_order_creation()
    {
        $data = [
            'store_id' => 1,
            'customer_id' => 5,
            'date' => now(),
            'payment_term_id' => 1,
            'additional_cost' => 0,
            'discount' => 0,
            'order_items' =>[
                [
                    'item_id' => 1,
                    'quantity' => 2,
                    'unit_price' => 50.00,
                    'total_price' => 100.00,
                ],
                [
                    'item_id' => 2,
                    'quantity' => 1,
                    'unit_price' => 150.00,
                    'total_price' => 150.00,
                ],
            ],
        ];

        $response = $this->postJson('/api/sales/orders', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('sales_orders', [
            'customer_id' => 5,
            'store_id' => 1,
        ]);
    }
}
