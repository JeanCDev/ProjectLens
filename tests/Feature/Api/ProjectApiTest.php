<?php

namespace Tests\Feature\Api;

use App\Enums\ProjectStatus;
use App\Jobs\AnalyzeProjectJob;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_unauthenticated_requests_are_rejected(): void
    {
        $this->getJson('/api/projects')->assertStatus(401);
    }

    public function test_index_returns_paginated_projects(): void
    {
        Project::factory()->count(3)->create();

        $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/projects')
            ->assertOk()
            ->assertJsonStructure(['data' => [['id', 'name', 'status', 'status_label']]])
            ->assertJsonCount(3, 'data');
    }

    public function test_index_filters_by_status(): void
    {
        Project::factory()->create(['status' => ProjectStatus::Active]);
        Project::factory()->create(['status' => ProjectStatus::Archived]);

        $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/projects?status=active')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', 'active');
    }

    public function test_index_filters_by_search(): void
    {
        Project::factory()->create(['name' => 'Foguete Espacial']);
        Project::factory()->create(['name' => 'App de Compras']);

        $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/projects?search=espacial')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Foguete Espacial');
    }

    public function test_index_filters_by_language(): void
    {
        Project::factory()->create(['primary_language' => 'PHP']);
        Project::factory()->create(['primary_language' => 'Go']);

        $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/projects?language=PHP')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_store_creates_project(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/projects', [
                'name' => 'Novo Sistema',
                'status' => 'planning',
                'start_date' => '2026-01-01',
                'estimated_end_date' => '2026-12-31',
            ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Novo Sistema')
            ->assertJsonPath('data.status', 'planning')
            ->assertJsonPath('data.status_label', 'Planejamento');

        $this->assertDatabaseHas('projects', ['name' => 'Novo Sistema']);
    }

    public function test_store_validates_name(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/projects', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    public function test_store_validates_status_enum(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/projects', ['name' => 'X', 'status' => 'nao_existe'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('status');
    }

    public function test_store_dispatches_analyze_job(): void
    {
        Queue::fake();

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/projects', ['name' => 'Com Job'])
            ->assertCreated();

        Queue::assertPushed(AnalyzeProjectJob::class);
    }

    public function test_show_returns_project(): void
    {
        $project = Project::factory()->create();

        $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/projects/{$project->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $project->id)
            ->assertJsonPath('data.name', $project->name);
    }

    public function test_show_returns_404_for_missing(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/projects/99999')
            ->assertNotFound();
    }

    public function test_update_modifies_project(): void
    {
        $project = Project::factory()->create(['name' => 'Antigo']);

        $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/projects/{$project->id}", ['name' => 'Novo'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Novo');

        $this->assertDatabaseHas('projects', ['id' => $project->id, 'name' => 'Novo']);
    }

    public function test_patch_modifies_project(): void
    {
        $project = Project::factory()->create(['name' => 'Antigo']);

        $this->actingAs($this->user, 'sanctum')
            ->patchJson("/api/projects/{$project->id}", ['status' => 'completed'])
            ->assertOk()
            ->assertJsonPath('data.status', 'completed');
    }

    public function test_update_validates_enum(): void
    {
        $project = Project::factory()->create();

        $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/projects/{$project->id}", ['status' => 'inválido'])
            ->assertStatus(422);
    }

    public function test_destroy_deletes_project(): void
    {
        $project = Project::factory()->create();

        $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/projects/{$project->id}")
            ->assertStatus(204);

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }
}
