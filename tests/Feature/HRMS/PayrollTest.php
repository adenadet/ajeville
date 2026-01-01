<?php

namespace Tests\Feature\HRMS;

use Tests\TestCase;

class PayrollTest extends TestCase
{
    public function test_payroll_module_present_or_marked()
    {
        // Payroll module is required by project spec. Marking as incomplete so engineers will implement tests when module exists.
        $this->markTestIncomplete('Payroll module tests not implemented - implement payroll routes, controllers and update this test.');
    }
}
