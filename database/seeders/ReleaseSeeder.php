<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Release;
use Illuminate\Database\Seeder;

class ReleaseSeeder extends Seeder
{
    public function run(): void
    {
        Project::all()->each(function (Project $project) {
            $project->releases()->saveMany(
                Release::factory()->count(rand(1, 5))->make([
                    'project_id' => $project->id,
                ])
            );
        });
    }
}
