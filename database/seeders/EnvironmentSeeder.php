<?php

namespace Database\Seeders;

use App\Models\Environment;
use App\Models\Project;
use Illuminate\Database\Seeder;

class EnvironmentSeeder extends Seeder
{
    public function run(): void
    {
        Project::all()->each(function (Project $project) {
            $project->environments()->saveMany(
                Environment::factory()->count(rand(1, 3))->make([
                    'project_id' => $project->id,
                ])
            );
        });
    }
}
