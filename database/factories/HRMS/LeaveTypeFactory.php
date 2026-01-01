<?php

namespace Database\Factories\HRMS;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hrms\LeaveType>
 */
class LeaveTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'AL '.date('Y').' 15', 
            'leave_category' => 'Working Days', 
            'no_of_days' => 15, 
            'status' => 1, 
            'start_date' => date('Y').'-01-01', 
            'end_date' => date('Y', strtotime('+ 1 year')).'-03-01', 
            'created_by' => 1, 
            'updated_by' => 1,
        ];
    }
}
