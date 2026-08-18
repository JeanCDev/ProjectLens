<?php

namespace Tests\Unit\Policies;

use App\Models\Environment;
use App\Models\User;
use App\Policies\EnvironmentPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnvironmentPolicyTest extends TestCase
{
    use RefreshDatabase;

    private EnvironmentPolicy $policy;

    private User $user;

    private Environment $environment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new EnvironmentPolicy;
        $this->user = User::factory()->create();
        $this->environment = Environment::factory()->create();
    }

    public function test_all_methods_allow_authenticated_user(): void
    {
        $this->assertTrue($this->policy->viewAny($this->user));
        $this->assertTrue($this->policy->view($this->user, $this->environment));
        $this->assertTrue($this->policy->create($this->user));
        $this->assertTrue($this->policy->update($this->user, $this->environment));
        $this->assertTrue($this->policy->delete($this->user, $this->environment));
    }
}
