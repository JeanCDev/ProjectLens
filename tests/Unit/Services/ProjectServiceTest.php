<?php

namespace Tests\Unit\Services;

use App\Enums\ProjectStatus;
use App\Models\Environment;
use App\Models\Project;
use App\Models\Release;
use App\Services\ProjectService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProjectService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProjectService;
    }

    public function test_list_returns_paginated_projects(): void
    {
        Project::factory()->count(20)->create();

        $result = $this->service->list();

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(15, $result->items());
        $this->assertSame(20, $result->total());
    }

    public function test_list_filters_by_status(): void
    {
        Project::factory()->create(['status' => ProjectStatus::Active]);
        Project::factory()->create(['status' => ProjectStatus::Archived]);

        $result = $this->service->list(['status' => 'active']);

        $this->assertSame(1, $result->total());
        $this->assertSame(ProjectStatus::Active, $result->first()->status);
    }

    public function test_list_filters_by_search_on_name(): void
    {
        Project::factory()->create(['name' => 'Sistema Beta']);
        Project::factory()->create(['name' => 'Outro Projeto']);

        $result = $this->service->list(['search' => 'beta']);

        $this->assertSame(1, $result->total());
        $this->assertSame('Sistema Beta', $result->first()->name);
    }

    public function test_list_filters_by_language(): void
    {
        Project::factory()->create(['primary_language' => 'PHP']);
        Project::factory()->create(['primary_language' => 'Go']);

        $result = $this->service->list(['language' => 'PHP']);

        $this->assertSame(1, $result->total());
    }

    public function test_list_eager_loads_relations(): void
    {
        $project = Project::factory()->create();
        Environment::factory()->create(['project_id' => $project->id]);
        Release::factory()->create(['project_id' => $project->id]);

        $result = $this->service->list();

        $this->assertTrue($result->first()->relationLoaded('environments'));
        $this->assertTrue($result->first()->relationLoaded('releases'));
        $this->assertTrue($result->first()->relationLoaded('teamMembers'));
    }

    public function test_create_persists_project(): void
    {
        $project = $this->service->create([
            'name' => 'Novo Projeto',
            'description' => 'Descrição',
            'status' => 'planning',
        ]);

        $this->assertDatabaseHas('projects', ['name' => 'Novo Projeto']);
        $this->assertSame(ProjectStatus::Planning, $project->status);
    }

    public function test_find_returns_project_with_relations(): void
    {
        $project = Project::factory()->create();
        Environment::factory()->create(['project_id' => $project->id]);

        $found = $this->service->find($project->id);

        $this->assertTrue($found->is($project));
        $this->assertTrue($found->relationLoaded('environments'));
        $this->assertTrue($found->relationLoaded('releases'));
        $this->assertTrue($found->relationLoaded('endpoints'));
        $this->assertTrue($found->relationLoaded('teamMembers'));
    }

    public function test_find_throws_when_missing(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->find(99999);
    }

    public function test_update_updates_and_refreshes(): void
    {
        $project = Project::factory()->create(['name' => 'Original']);

        $updated = $this->service->update($project, ['name' => 'Atualizado']);

        $this->assertSame('Atualizado', $updated->name);
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'name' => 'Atualizado']);
        $this->assertTrue($updated->relationLoaded('environments'));
        $this->assertTrue($updated->relationLoaded('teamMembers'));
    }

    public function test_delete_removes_project(): void
    {
        $project = Project::factory()->create();

        $result = $this->service->delete($project);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_by_status_returns_paginated(): void
    {
        Project::factory()->count(3)->create(['status' => ProjectStatus::Active]);
        Project::factory()->create(['status' => ProjectStatus::Archived]);

        $result = $this->service->byStatus(ProjectStatus::Active);

        $this->assertSame(3, $result->total());
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }
}
