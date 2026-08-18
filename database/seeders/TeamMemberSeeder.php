<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeamMemberSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::factory()->count(5)->create();

        Project::all()->each(function (Project $project) use ($users) {
            $randomUsers = $users->random(min(3, $users->count()));

            $randomUsers->each(function (User $user) use ($project) {
                $project->teamMembers()->create([
                    'user_id' => $user->id,
                    'role' => fake()->randomElement(['admin', 'developer', 'designer', 'devops', 'manager', 'viewer']),
                ]);
            });
        });
    }
}
