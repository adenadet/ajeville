<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        //\App\Models\User::factory(10)->create();
        //\App\Models\Escrows\Product::factory(200)->create();
        //\App\Models\Escrows\Transaction::factory(30)->create();
        //\App\Models\Invoice\Invoice::factory(20)->create();
        //\App\Models\Invoice\Item::factory(200)->create();
        \App\Models\Hrms\LeaveRequest::factory(100)->create();
    }
}
