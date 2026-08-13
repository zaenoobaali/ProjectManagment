<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       return [
            'project_name' => $this->faker->sentence(3),
            'description'  => $this->faker->paragraph(),
            'start_date'   => '2026-01-01',
            'end_date'     => '2026-12-31',
            'status'       => 'in-progress',
            'created_by'   => User::factory(), // سيقوم بربطه بمستخدم أو نحدده يدوياً
        ];
    }
}
