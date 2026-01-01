<?php

namespace Tests\Feature\Finance;

use App\Models\Finance\Order;
use App\Models\Finance\Invoice;
use App\Models\Finance\Payment;
use App\Models\Finance\PaymentAllocation;
use App\Services\Finance\PaymentAllocationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Mockery;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;
    
}
