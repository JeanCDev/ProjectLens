<?php

namespace Tests\Feature\Api;

use App\Enums\EndpointStatus;
use App\Enums\HttpMethod;
use App\Models\ApiEndpoint;
use App\Models\Environment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiEndpointApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Project $project;

    private Environment $environment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->project = Project::factory()->create();
        $this->environment = Environment::factory()->create(['project_id' => $this->project->id]);
    }

    public function test_index_returns_endpoints(): void
    {
        ApiEndpoint::factory()->count(2)->create([
            'project_id' => $this->project->id,
            'environment_id' => $this->environment->id,
        ]);

        $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/projects/{$this->project->id}/endpoints")
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_index_filters_by_method(): void
    {
        ApiEndpoint::factory()->create([
            'project_id' => $this->project->id,
            'environment_id' => $this->environment->id,
            'method' => HttpMethod::Get,
        ]);
        ApiEndpoint::factory()->create([
            'project_id' => $this->project->id,
            'environment_id' => $this->environment->id,
            'method' => HttpMethod::Post,
        ]);

        $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/projects/{$this->project->id}/endpoints?method=GET")
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_index_filters_by_status(): void
    {
        ApiEndpoint::factory()->create([
            'project_id' => $this->project->id,
            'environment_id' => $this->environment->id,
            'status' => EndpointStatus::Healthy,
        ]);
        ApiEndpoint::factory()->create([
            'project_id' => $this->project->id,
            'environment_id' => $this->environment->id,
            'status' => EndpointStatus::Down,
        ]);

        $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/projects/{$this->project->id}/endpoints?status=down")
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_store_creates_endpoint(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/projects/{$this->project->id}/endpoints", [
                'environment_id' => $this->environment->id,
                'name' => 'Health Check',
                'method' => 'GET',
                'url' => '/api/health',
                'status' => 'healthy',
            ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Health Check')
            ->assertJsonPath('data.method', 'GET');

        $this->assertDatabaseHas('api_endpoints', ['name' => 'Health Check', 'project_id' => $this->project->id]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/projects/{$this->project->id}/endpoints", [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['environment_id', 'name', 'method', 'url']);
    }

    public function test_store_validates_environment_exists(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/projects/{$this->project->id}/endpoints", [
                'environment_id' => 99999,
                'name' => 'X',
                'method' => 'GET',
                'url' => '/x',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('environment_id');
    }

    public function test_show_returns_endpoint_with_environment(): void
    {
        $endpoint = ApiEndpoint::factory()->create([
            'project_id' => $this->project->id,
            'environment_id' => $this->environment->id,
        ]);

        $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/projects/{$this->project->id}/endpoints/{$endpoint->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $endpoint->id)
            ->assertJsonStructure(['data' => ['environment']]);
    }

    public function test_show_returns_404_for_endpoint_of_other_project(): void
    {
        $other = Project::factory()->create();
        $otherEnv = Environment::factory()->create(['project_id' => $other->id]);
        $endpoint = ApiEndpoint::factory()->create([
            'project_id' => $other->id,
            'environment_id' => $otherEnv->id,
        ]);

        $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/projects/{$this->project->id}/endpoints/{$endpoint->id}")
            ->assertNotFound();
    }

    public function test_update_modifies_endpoint(): void
    {
        $endpoint = ApiEndpoint::factory()->create([
            'project_id' => $this->project->id,
            'environment_id' => $this->environment->id,
            'status' => EndpointStatus::Healthy,
        ]);

        $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/projects/{$this->project->id}/endpoints/{$endpoint->id}", ['status' => 'down'])
            ->assertOk()
            ->assertJsonPath('data.status', 'down');
    }

    public function test_destroy_deletes_endpoint(): void
    {
        $endpoint = ApiEndpoint::factory()->create([
            'project_id' => $this->project->id,
            'environment_id' => $this->environment->id,
        ]);

        $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/projects/{$this->project->id}/endpoints/{$endpoint->id}")
            ->assertStatus(204);

        $this->assertDatabaseMissing('api_endpoints', ['id' => $endpoint->id]);
    }
}
