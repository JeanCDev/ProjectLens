<?php

namespace Tests\Feature\Api;

use App\Enums\EnvironmentStatus;
use App\Models\Environment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnvironmentApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->project = Project::factory()->create();
    }

    public function test_index_returns_environments(): void
    {
        Environment::factory()->count(2)->create(['project_id' => $this->project->id]);

        $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/projects/{$this->project->id}/environments")
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_store_creates_environment(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/projects/{$this->project->id}/environments", [
                'name' => 'Production',
                'status' => 'active',
            ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Production');

        $this->assertDatabaseHas('environments', ['name' => 'Production', 'project_id' => $this->project->id]);
    }

    public function test_store_validates_name(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/projects/{$this->project->id}/environments", [])
            ->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    public function test_show_returns_environment(): void
    {
        $environment = Environment::factory()->create(['project_id' => $this->project->id]);

        $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/projects/{$this->project->id}/environments/{$environment->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $environment->id);
    }

    public function test_update_modifies_environment(): void
    {
        $environment = Environment::factory()->create([
            'project_id' => $this->project->id,
            'status' => EnvironmentStatus::Inactive,
        ]);

        $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/projects/{$this->project->id}/environments/{$environment->id}", ['status' => 'maintenance'])
            ->assertOk()
            ->assertJsonPath('data.status', 'maintenance');
    }

    public function test_destroy_deletes_environment(): void
    {
        $environment = Environment::factory()->create(['project_id' => $this->project->id]);

        $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/projects/{$this->project->id}/environments/{$environment->id}")
            ->assertStatus(204);

        $this->assertDatabaseMissing('environments', ['id' => $environment->id]);
    }
}
