<?php

namespace Tests\Unit\Policies;

use App\Models\ApiEndpoint;
use App\Models\User;
use App\Policies\ApiEndpointPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiEndpointPolicyTest extends TestCase
{
    use RefreshDatabase;

    private ApiEndpointPolicy $policy;

    private User $user;

    private ApiEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new ApiEndpointPolicy;
        $this->user = User::factory()->create();
        $this->endpoint = ApiEndpoint::factory()->create();
    }

    public function test_all_methods_allow_authenticated_user(): void
    {
        $this->assertTrue($this->policy->viewAny($this->user));
        $this->assertTrue($this->policy->view($this->user, $this->endpoint));
        $this->assertTrue($this->policy->create($this->user));
        $this->assertTrue($this->policy->update($this->user, $this->endpoint));
        $this->assertTrue($this->policy->delete($this->user, $this->endpoint));
    }
}
