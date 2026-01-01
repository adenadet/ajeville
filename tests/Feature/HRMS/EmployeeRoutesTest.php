<?php

namespace Tests\Feature\HRMS;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class EmployeeRoutesTest extends TestCase
{
    public function test_employee_routes_and_controllers_exist()
    {
        // Check key route names expected for the HRMS employee area
        $this->assertTrue(Route::has('employees.index'), 'Route employees.index not defined');
        $this->assertTrue(Route::has('employees.search'), 'Route employees.search not defined');
        $this->assertTrue(Route::has('employees.import'), 'Route employees.import not defined');
        $this->assertTrue(Route::has('employees.update_status'), 'Route employees.update_status not defined');

        // Check controller file presence (file path relative to base_path())
        $controllerPath = base_path('app/Http/Controllers/EmployeeController.php');
        $this->assertFileExists($controllerPath, 'EmployeeController.php not found at ' . $controllerPath);
    }
}
