<?php

namespace Database\Factories;

use App\Enums\ReleaseStatus;
use App\Models\Project;
use App\Models\Release;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Release>
 */
class ReleaseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'version' => fake()->semver(),
            'changelog' => fake()->paragraphs(3, true),
            'released_at' => fake()->optional(0.7)->dateTimeBetween('-1 year', 'now'),
            'status' => fake()->randomElement(ReleaseStatus::cases()),
        ];
    }
}
