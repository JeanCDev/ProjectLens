<?php

namespace Tests\Unit\Jobs;

use App\Enums\ProjectStatus;
use App\Jobs\AnalyzeProjectJob;
use App\Models\ApiEndpoint;
use App\Models\Environment;
use App\Models\Project;
use App\Models\Release;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyzeProjectJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_loads_relations_and_updates_project(): void
    {
        $project = Project::factory()->create(['status' => ProjectStatus::Planning]);
        $environment = Environment::factory()->create(['project_id' => $project->id]);
        $release = Release::factory()->create(['project_id' => $project->id]);
        ApiEndpoint::factory()->create([
            'project_id' => $project->id,
            'environment_id' => $environment->id,
        ]);

        $job = new AnalyzeProjectJob($project);
        $job->handle();

        $this->assertTrue($project->relationLoaded('environments'));
        $this->assertTrue($project->relationLoaded('releases'));
        $this->assertTrue($project->relationLoaded('endpoints'));

        $this->assertDatabaseHas('projects', ['id' => $project->id, 'status' => 'planning']);
    }
}
