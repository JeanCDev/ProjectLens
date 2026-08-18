<?php

namespace Tests\Unit\Policies;

use App\Models\Project;
use App\Models\User;
use App\Policies\ProjectPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectPolicyTest extends TestCase
{
    use RefreshDatabase;

    private ProjectPolicy $policy;

    private User $user;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new ProjectPolicy;
        $this->user = User::factory()->create();
        $this->project = Project::factory()->create();
    }

    public function test_all_methods_allow_authenticated_user(): void
    {
        $this->assertTrue($this->policy->viewAny($this->user));
        $this->assertTrue($this->policy->view($this->user, $this->project));
        $this->assertTrue($this->policy->create($this->user));
        $this->assertTrue($this->policy->update($this->user, $this->project));
        $this->assertTrue($this->policy->delete($this->user, $this->project));
    }
}
