<?php

namespace Database\Factories;

use App\Enums\EnvironmentStatus;
use App\Models\Environment;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Environment>
 */
class EnvironmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'name' => fake()->randomElement(['Production', 'Staging', 'Development', 'Testing', 'QA']),
            'url' => fake()->url(),
            'database' => fake()->randomElement(['MySQL 8.0', 'PostgreSQL 15', 'SQLite', 'MongoDB 6.0', 'Redis 7']),
            'version' => fake()->semver(),
            'status' => fake()->randomElement(EnvironmentStatus::cases()),
        ];
    }
}
