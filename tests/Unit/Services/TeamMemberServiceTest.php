<?php

namespace Tests\Unit\Services;

use App\Enums\TeamMemberRole;
use App\Models\Project;
use App\Models\TeamMember;
use App\Models\User;
use App\Services\TeamMemberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamMemberServiceTest extends TestCase
{
    use RefreshDatabase;

    private TeamMemberService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TeamMemberService;
    }

    public function test_list_by_project_returns_paginated_with_user(): void
    {
        $project = Project::factory()->create();
        $user = User::factory()->create();
        TeamMember::factory()->create(['project_id' => $project->id, 'user_id' => $user->id]);

        $result = $this->service->listByProject($project);

        $this->assertSame(1, $result->total());
        $this->assertTrue($result->first()->relationLoaded('user'));
    }

    public function test_create_loads_user(): void
    {
        $project = Project::factory()->create();
        $user = User::factory()->create();

        $member = $this->service->create($project, [
            'user_id' => $user->id,
            'role' => TeamMemberRole::Developer->value,
        ]);

        $this->assertSame($project->id, $member->project_id);
        $this->assertTrue($member->relationLoaded('user'));
        $this->assertTrue($member->user->is($user));
    }

    public function test_find_returns_member_with_user(): void
    {
        $project = Project::factory()->create();
        $member = TeamMember::factory()->create(['project_id' => $project->id]);

        $found = $this->service->find($project, $member->id);

        $this->assertTrue($found->is($member));
        $this->assertTrue($found->relationLoaded('user'));
    }

    public function test_update_modifies_member_and_loads_user(): void
    {
        $project = Project::factory()->create();
        $user = User::factory()->create();
        $member = TeamMember::factory()->create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'role' => TeamMemberRole::Developer,
        ]);

        $updated = $this->service->update($member, ['role' => TeamMemberRole::Manager->value]);

        $this->assertSame(TeamMemberRole::Manager, $updated->role);
        $this->assertTrue($updated->relationLoaded('user'));
    }

    public function test_delete_removes_member(): void
    {
        $member = TeamMember::factory()->create();

        $result = $this->service->delete($member);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('team_members', ['id' => $member->id]);
    }
}
