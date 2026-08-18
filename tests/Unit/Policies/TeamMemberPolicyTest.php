<?php

namespace Tests\Unit\Policies;

use App\Models\TeamMember;
use App\Models\User;
use App\Policies\TeamMemberPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamMemberPolicyTest extends TestCase
{
    use RefreshDatabase;

    private TeamMemberPolicy $policy;

    private User $user;

    private TeamMember $member;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new TeamMemberPolicy;
        $this->user = User::factory()->create();
        $this->member = TeamMember::factory()->create();
    }

    public function test_all_methods_allow_authenticated_user(): void
    {
        $this->assertTrue($this->policy->viewAny($this->user));
        $this->assertTrue($this->policy->view($this->user, $this->member));
        $this->assertTrue($this->policy->create($this->user));
        $this->assertTrue($this->policy->update($this->user, $this->member));
        $this->assertTrue($this->policy->delete($this->user, $this->member));
    }
}
