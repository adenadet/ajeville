<?php

namespace Database\Factories\Hrms;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hrms\LeaveRequest>
 */
class LeaveRequestFactory extends Factory
{
   public function definition(): array
    {
        return [
            'user_id' => rand(1, 2957), 
            'leave_type_id' => rand(1, 10), 
            'from_date' => fake()->dateTimeThisMonth(), 
            'to_date' => fake()->dateTimeThisYear(), 
            'reason' => fake()->paragraph(), 
            'remarks' => fake()->paragraph(), 
            'status' => 0, 
            'is_half_day' => 0, 
            'leave_attachment' => null, 
            'created_by' => rand(1, 25), 
            'updated_by' => rand(1, 25),
        ];
    }
}
