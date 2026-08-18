<?php

namespace Tests\Unit\Observers;

use App\Jobs\AnalyzeProjectJob;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProjectObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_created_project_dispatches_job(): void
    {
        Queue::fake();

        $project = Project::factory()->create();

        Queue::assertPushed(AnalyzeProjectJob::class, fn (AnalyzeProjectJob $job) => $job->project->is($project));
    }

    public function test_updated_project_dispatches_job(): void
    {
        Queue::fake();

        $project = Project::factory()->create();
        $project->update(['name' => 'Renomeado']);

        Queue::assertPushed(AnalyzeProjectJob::class, 2);
    }
}
