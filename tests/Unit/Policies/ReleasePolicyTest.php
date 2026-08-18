<?php

namespace Tests\Unit\Policies;

use App\Models\Release;
use App\Models\User;
use App\Policies\ReleasePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReleasePolicyTest extends TestCase
{
    use RefreshDatabase;

    private ReleasePolicy $policy;

    private User $user;

    private Release $release;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new ReleasePolicy;
        $this->user = User::factory()->create();
        $this->release = Release::factory()->create();
    }

    public function test_all_methods_allow_authenticated_user(): void
    {
        $this->assertTrue($this->policy->viewAny($this->user));
        $this->assertTrue($this->policy->view($this->user, $this->release));
        $this->assertTrue($this->policy->create($this->user));
        $this->assertTrue($this->policy->update($this->user, $this->release));
        $this->assertTrue($this->policy->delete($this->user, $this->release));
    }
}
