<?php

namespace Tests\Unit\Services;

use App\Models\Environment;
use App\Models\Project;
use App\Services\EnvironmentService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnvironmentServiceTest extends TestCase
{
    use RefreshDatabase;

    private EnvironmentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EnvironmentService;
    }

    public function test_list_by_project_returns_paginated(): void
    {
        $project = Project::factory()->create();
        Environment::factory()->count(2)->create(['project_id' => $project->id]);

        $result = $this->service->listByProject($project);

        $this->assertSame(2, $result->total());
    }

    public function test_create_attaches_to_project(): void
    {
        $project = Project::factory()->create();

        $environment = $this->service->create($project, ['name' => 'Prod']);

        $this->assertSame($project->id, $environment->project_id);
    }

    public function test_find_returns_environment_of_project(): void
    {
        $project = Project::factory()->create();
        $environment = Environment::factory()->create(['project_id' => $project->id]);

        $found = $this->service->find($project, $environment->id);

        $this->assertTrue($found->is($environment));
    }

    public function test_find_throws_for_environment_of_other_project(): void
    {
        $projectA = Project::factory()->create();
        $projectB = Project::factory()->create();
        $environment = Environment::factory()->create(['project_id' => $projectA->id]);

        $this->expectException(ModelNotFoundException::class);

        $this->service->find($projectB, $environment->id);
    }

    public function test_update_modifies_environment(): void
    {
        $environment = Environment::factory()->create(['name' => 'Dev']);

        $updated = $this->service->update($environment, ['name' => 'Stage']);

        $this->assertSame('Stage', $updated->name);
    }

    public function test_delete_removes_environment(): void
    {
        $environment = Environment::factory()->create();

        $result = $this->service->delete($environment);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('environments', ['id' => $environment->id]);
    }
}
