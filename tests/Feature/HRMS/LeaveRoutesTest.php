<?php

namespace Tests\Feature\HRMS;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LeaveRoutesTest extends TestCase
{
    public function test_leave_routes_exist()
    {
        $this->assertTrue(Route::has('leaves.index'), 'Route leaves.index not defined');
        $this->assertTrue(Route::has('leaves.confirm'), 'Route leaves.confirm not defined');
        $this->assertTrue(Route::has('leave_types.index'), 'Route leave_types.index not defined');

        $controllerPath = base_path('app/Http/Controllers/LeaveController.php');
        $this->assertFileExists($controllerPath, 'LeaveController.php not found at ' . $controllerPath);
    }
}
