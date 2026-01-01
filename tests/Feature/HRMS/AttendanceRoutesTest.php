<?php

namespace Tests\Feature\HRMS;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AttendanceRoutesTest extends TestCase
{
    public function test_clockin_and_attendance_routes_exist()
    {
        // resource name: clock_ins
        $this->assertTrue(Route::has('clock_ins.index'), 'Route clock_ins.index not defined');

        $controllerPath = base_path('app/Http/Controllers/ClockInController.php');
        $this->assertFileExists($controllerPath, 'ClockInController.php not found at ' . $controllerPath);
    }
}
