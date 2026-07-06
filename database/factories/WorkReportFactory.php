<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WorkReport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkReport>
 */
class WorkReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'input_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'latitude' => fake()->latitude(-8, -5),
            'longitude' => fake()->longitude(106, 112),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'work_plan' => fake()->sentence(8),
            'work_activity' => fake()->paragraph(),
            'work_result' => fake()->sentence(12),
        ];
    }
}
