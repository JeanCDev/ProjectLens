<?php

namespace Tests\Feature\Api;

use App\Enums\TeamMemberRole;
use App\Models\Project;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamMemberApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Project $project;

    private User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->project = Project::factory()->create();
        $this->otherUser = User::factory()->create();
    }

    public function test_index_returns_team_members(): void
    {
        TeamMember::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => $this->otherUser->id,
        ]);
        TeamMember::factory()->create([
            'project_id' => $this->project->id,
        ]);

        $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/projects/{$this->project->id}/team-members")
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure(['data' => [['user' => ['id', 'name', 'email']]]]);
    }

    public function test_store_creates_team_member(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/projects/{$this->project->id}/team-members", [
                'user_id' => $this->otherUser->id,
                'role' => TeamMemberRole::Developer->value,
            ])
            ->assertCreated()
            ->assertJsonPath('data.role', 'developer')
            ->assertJsonPath('data.role_label', 'Desenvolvedor')
            ->assertJsonPath('data.user.name', $this->otherUser->name);

        $this->assertDatabaseHas('team_members', [
            'project_id' => $this->project->id,
            'user_id' => $this->otherUser->id,
        ]);
    }

    public function test_store_validates_user_exists(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/projects/{$this->project->id}/team-members", [
                'user_id' => 99999,
                'role' => 'developer',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('user_id');
    }

    public function test_store_validates_role_enum(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/projects/{$this->project->id}/team-members", [
                'user_id' => $this->otherUser->id,
                'role' => 'team_lead',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('role');
    }

    public function test_show_returns_team_member(): void
    {
        $member = TeamMember::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => $this->otherUser->id,
        ]);

        $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/projects/{$this->project->id}/team-members/{$member->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $member->id)
            ->assertJsonPath('data.user.id', $this->otherUser->id);
    }

    public function test_update_modifies_team_member(): void
    {
        $member = TeamMember::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => $this->otherUser->id,
            'role' => TeamMemberRole::Developer,
        ]);

        $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/projects/{$this->project->id}/team-members/{$member->id}", ['role' => 'manager'])
            ->assertOk()
            ->assertJsonPath('data.role', 'manager')
            ->assertJsonPath('data.user.id', $this->otherUser->id);
    }

    public function test_destroy_deletes_team_member(): void
    {
        $member = TeamMember::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => $this->otherUser->id,
        ]);

        $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/projects/{$this->project->id}/team-members/{$member->id}")
            ->assertStatus(204);

        $this->assertDatabaseMissing('team_members', ['id' => $member->id]);
    }
}
