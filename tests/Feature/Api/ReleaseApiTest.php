<?php

namespace Tests\Feature\Api;

use App\Enums\ReleaseStatus;
use App\Models\Project;
use App\Models\Release;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReleaseApiTest extends TestCase
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

    public function test_index_returns_releases(): void
    {
        Release::factory()->count(2)->create(['project_id' => $this->project->id]);

        $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/projects/{$this->project->id}/releases")
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_index_filters_by_status(): void
    {
        Release::factory()->create(['project_id' => $this->project->id, 'status' => ReleaseStatus::Stable]);
        Release::factory()->create(['project_id' => $this->project->id, 'status' => ReleaseStatus::Draft]);

        $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/projects/{$this->project->id}/releases?status=stable")
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_store_creates_release(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/projects/{$this->project->id}/releases", [
                'version' => '3.0.0',
                'status' => 'stable',
            ])
            ->assertCreated()
            ->assertJsonPath('data.version', '3.0.0');

        $this->assertDatabaseHas('releases', ['version' => '3.0.0', 'project_id' => $this->project->id]);
    }

    public function test_store_validates_version(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/projects/{$this->project->id}/releases", [])
            ->assertStatus(422)
            ->assertJsonValidationErrors('version');
    }

    public function test_show_returns_release(): void
    {
        $release = Release::factory()->create(['project_id' => $this->project->id]);

        $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/projects/{$this->project->id}/releases/{$release->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $release->id);
    }

    public function test_show_returns_404_for_release_of_other_project(): void
    {
        $other = Project::factory()->create();
        $release = Release::factory()->create(['project_id' => $other->id]);

        $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/projects/{$this->project->id}/releases/{$release->id}")
            ->assertNotFound();
    }

    public function test_update_modifies_release(): void
    {
        $release = Release::factory()->create(['project_id' => $this->project->id, 'version' => '1.0.0']);

        $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/projects/{$this->project->id}/releases/{$release->id}", ['version' => '2.0.0'])
            ->assertOk()
            ->assertJsonPath('data.version', '2.0.0');
    }

    public function test_destroy_deletes_release(): void
    {
        $release = Release::factory()->create(['project_id' => $this->project->id]);

        $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/projects/{$this->project->id}/releases/{$release->id}")
            ->assertStatus(204);

        $this->assertDatabaseMissing('releases', ['id' => $release->id]);
    }
}
