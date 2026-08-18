<?php

namespace Database\Seeders;

use App\Models\ApiEndpoint;
use App\Models\Environment;
use Illuminate\Database\Seeder;

class ApiEndpointSeeder extends Seeder
{
    public function run(): void
    {
        Environment::all()->each(function (Environment $environment) {
            $environment->endpoints()->saveMany(
                ApiEndpoint::factory()->count(rand(2, 6))->make([
                    'project_id' => $environment->project_id,
                    'environment_id' => $environment->id,
                ])
            );
        });
    }
}
