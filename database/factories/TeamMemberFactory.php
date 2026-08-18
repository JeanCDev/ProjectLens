<?php

namespace Database\Factories;

use App\Enums\TeamMemberRole;
use App\Models\Project;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeamMember>
 */
class TeamMemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'user_id' => User::factory(),
            'role' => fake()->randomElement(TeamMemberRole::cases()),
        ];
    }
}
