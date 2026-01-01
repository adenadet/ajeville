<?php

namespace Tests\Feature\HRMS;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TrainingRoutesTest extends TestCase
{
    public function test_training_routes_and_controller()
    {
        $this->assertTrue(Route::has('trainings.index'), 'Route trainings.index not defined');

        $controllerPath = base_path('app/Http/Controllers/TrainingController.php');
        $this->assertFileExists($controllerPath, 'TrainingController.php not found at ' . $controllerPath);
    }
}
