<?php

namespace Database\Factories;

use App\Enums\EndpointStatus;
use App\Enums\HttpMethod;
use App\Models\ApiEndpoint;
use App\Models\Environment;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApiEndpoint>
 */
class ApiEndpointFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'environment_id' => Environment::factory(),
            'name' => fake()->unique()->words(3, true),
            'method' => fake()->randomElement(HttpMethod::cases()),
            'url' => '/'.fake()->slug(2).'/'.fake()->uuid(),
            'status' => fake()->randomElement(EndpointStatus::cases()),
            'last_checked_at' => fake()->optional(0.8)->dateTimeBetween('-1 day', 'now'),
            'response_time_ms' => fake()->optional(0.7)->numberBetween(50, 3000),
        ];
    }
}
