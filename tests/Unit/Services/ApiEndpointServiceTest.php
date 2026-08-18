<?php

namespace Tests\Unit\Services;

use App\Models\ApiEndpoint;
use App\Models\Environment;
use App\Models\Project;
use App\Services\ApiEndpointService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiEndpointServiceTest extends TestCase
{
    use RefreshDatabase;

    private ApiEndpointService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ApiEndpointService;
    }

    public function test_list_by_project_returns_paginated_with_environment(): void
    {
        $project = Project::factory()->create();
        $environment = Environment::factory()->create(['project_id' => $project->id]);
        ApiEndpoint::factory()->count(2)->create([
            'project_id' => $project->id,
            'environment_id' => $environment->id,
        ]);

        $result = $this->service->listByProject($project);

        $this->assertSame(2, $result->total());
        $this->assertTrue($result->first()->relationLoaded('environment'));
    }

    public function test_list_by_project_filters_by_status_and_method(): void
    {
        $project = Project::factory()->create();
        $environment = Environment::factory()->create(['project_id' => $project->id]);
        ApiEndpoint::factory()->create([
            'project_id' => $project->id,
            'environment_id' => $environment->id,
            'status' => 'healthy',
            'method' => 'GET',
        ]);
        ApiEndpoint::factory()->create([
            'project_id' => $project->id,
            'environment_id' => $environment->id,
            'status' => 'down',
            'method' => 'POST',
        ]);

        $result = $this->service->listByProject($project, ['status' => 'healthy', 'method' => 'GET']);

        $this->assertSame(1, $result->total());
    }

    public function test_create_attaches_to_project(): void
    {
        $project = Project::factory()->create();
        $environment = Environment::factory()->create(['project_id' => $project->id]);

        $endpoint = $this->service->create($project, [
            'environment_id' => $environment->id,
            'name' => 'Health',
            'method' => 'GET',
            'url' => '/health',
        ]);

        $this->assertSame($project->id, $endpoint->project_id);
    }

    public function test_find_returns_endpoint_with_environment(): void
    {
        $project = Project::factory()->create();
        $environment = Environment::factory()->create(['project_id' => $project->id]);
        $endpoint = ApiEndpoint::factory()->create([
            'project_id' => $project->id,
            'environment_id' => $environment->id,
        ]);

        $found = $this->service->find($project, $endpoint->id);

        $this->assertTrue($found->is($endpoint));
        $this->assertTrue($found->relationLoaded('environment'));
    }

    public function test_find_throws_for_endpoint_of_other_project(): void
    {
        $projectA = Project::factory()->create();
        $projectB = Project::factory()->create();
        $environment = Environment::factory()->create(['project_id' => $projectA->id]);
        $endpoint = ApiEndpoint::factory()->create([
            'project_id' => $projectA->id,
            'environment_id' => $environment->id,
        ]);

        $this->expectException(ModelNotFoundException::class);

        $this->service->find($projectB, $endpoint->id);
    }

    public function test_update_modifies_endpoint(): void
    {
        $endpoint = ApiEndpoint::factory()->create(['name' => 'Old']);

        $updated = $this->service->update($endpoint, ['name' => 'New']);

        $this->assertSame('New', $updated->name);
    }

    public function test_delete_removes_endpoint(): void
    {
        $endpoint = ApiEndpoint::factory()->create();

        $result = $this->service->delete($endpoint);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('api_endpoints', ['id' => $endpoint->id]);
    }
}
