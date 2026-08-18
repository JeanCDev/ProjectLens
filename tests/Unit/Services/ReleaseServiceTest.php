<?php

namespace Tests\Unit\Services;

use App\Enums\ReleaseStatus;
use App\Models\Project;
use App\Models\Release;
use App\Services\ReleaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReleaseServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReleaseService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ReleaseService;
    }

    public function test_list_by_project_returns_paginated(): void
    {
        $project = Project::factory()->create();
        Release::factory()->count(3)->create(['project_id' => $project->id]);

        $result = $this->service->listByProject($project);

        $this->assertSame(3, $result->total());
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function test_list_by_project_filters_by_status(): void
    {
        $project = Project::factory()->create();
        Release::factory()->create(['project_id' => $project->id, 'status' => ReleaseStatus::Stable]);
        Release::factory()->create(['project_id' => $project->id, 'status' => ReleaseStatus::Draft]);

        $result = $this->service->listByProject($project, ['status' => 'stable']);

        $this->assertSame(1, $result->total());
    }

    public function test_list_by_project_only_returns_that_projects_releases(): void
    {
        $projectA = Project::factory()->create();
        $projectB = Project::factory()->create();
        Release::factory()->create(['project_id' => $projectA->id]);
        Release::factory()->count(2)->create(['project_id' => $projectB->id]);

        $result = $this->service->listByProject($projectA);

        $this->assertSame(1, $result->total());
    }

    public function test_create_attaches_to_project(): void
    {
        $project = Project::factory()->create();

        $release = $this->service->create($project, ['version' => '2.0.0', 'status' => 'stable']);

        $this->assertSame($project->id, $release->project_id);
        $this->assertDatabaseHas('releases', ['id' => $release->id, 'project_id' => $project->id]);
    }

    public function test_find_returns_release_of_project(): void
    {
        $project = Project::factory()->create();
        $release = Release::factory()->create(['project_id' => $project->id]);

        $found = $this->service->find($project, $release->id);

        $this->assertTrue($found->is($release));
    }

    public function test_find_throws_for_release_of_other_project(): void
    {
        $projectA = Project::factory()->create();
        $projectB = Project::factory()->create();
        $release = Release::factory()->create(['project_id' => $projectA->id]);

        $this->expectException(ModelNotFoundException::class);

        $this->service->find($projectB, $release->id);
    }

    public function test_update_modifies_release(): void
    {
        $release = Release::factory()->create(['version' => '1.0.0']);

        $updated = $this->service->update($release, ['version' => '1.1.0']);

        $this->assertSame('1.1.0', $updated->version);
    }

    public function test_delete_removes_release(): void
    {
        $release = Release::factory()->create();

        $result = $this->service->delete($release);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('releases', ['id' => $release->id]);
    }
}
