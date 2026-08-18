<?php

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'description' => fake()->paragraph(),
            'primary_language' => fake()->randomElement(['PHP', 'JavaScript', 'TypeScript', 'Python', 'Go', 'Rust', 'Java', 'C#']),
            'framework' => fake()->randomElement(['Laravel', 'Django', 'Spring Boot', 'Next.js', 'Vue.js', 'Rails', 'Flask', null]),
            'status' => fake()->randomElement(ProjectStatus::cases()),
            'start_date' => fake()->optional(0.8)->dateTimeBetween('-2 years', 'now'),
            'estimated_end_date' => fake()->optional(0.6)->dateTimeBetween('now', '+1 year'),
            'git_repository' => fake()->optional(0.9)->url(),
            'documentation_url' => fake()->optional(0.7)->url(),
        ];
    }
}
