<?php

namespace Tests\Unit\Models;

use App\Enums\TeamMemberRole;
use App\Models\Project;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamMemberModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_is_cast_to_enum(): void
    {
        $member = TeamMember::factory()->create(['role' => TeamMemberRole::DevOps]);

        $this->assertInstanceOf(TeamMemberRole::class, $member->role);
        $this->assertSame(TeamMemberRole::DevOps, $member->role);
    }

    public function test_belongs_to_project(): void
    {
        $project = Project::factory()->create();
        $member = TeamMember::factory()->create(['project_id' => $project->id]);

        $this->assertTrue($member->project->is($project));
    }

    public function test_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $member = TeamMember::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($member->user->is($user));
    }
}
