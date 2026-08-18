<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\StoreApiEndpointRequest;
use App\Http\Requests\StoreEnvironmentRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\StoreReleaseRequest;
use App\Http\Requests\StoreTeamMemberRequest;
use App\Http\Requests\UpdateApiEndpointRequest;
use App\Http\Requests\UpdateEnvironmentRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Requests\UpdateReleaseRequest;
use App\Http\Requests\UpdateTeamMemberRequest;
use App\Models\Environment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class RequestRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_requests_are_authorized(): void
    {
        $this->assertTrue((new StoreProjectRequest)->authorize());
        $this->assertTrue((new UpdateProjectRequest)->authorize());
        $this->assertTrue((new StoreReleaseRequest)->authorize());
        $this->assertTrue((new UpdateReleaseRequest)->authorize());
        $this->assertTrue((new StoreEnvironmentRequest)->authorize());
        $this->assertTrue((new UpdateEnvironmentRequest)->authorize());
        $this->assertTrue((new StoreApiEndpointRequest)->authorize());
        $this->assertTrue((new UpdateApiEndpointRequest)->authorize());
        $this->assertTrue((new StoreTeamMemberRequest)->authorize());
        $this->assertTrue((new UpdateTeamMemberRequest)->authorize());
    }

    public function test_store_project_validates_required_name(): void
    {
        $validator = Validator::make([], (new StoreProjectRequest)->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function test_store_project_validates_enum_and_dates(): void
    {
        $validator = Validator::make([
            'name' => 'X',
            'status' => 'nao_existe',
            'estimated_end_date' => '2025-01-01',
            'start_date' => '2026-01-01',
        ], (new StoreProjectRequest)->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('status', $validator->errors()->toArray());
        $this->assertArrayHasKey('estimated_end_date', $validator->errors()->toArray());
    }

    public function test_store_project_passes_with_valid_data(): void
    {
        $validator = Validator::make([
            'name' => 'Projeto',
            'status' => 'active',
            'start_date' => '2026-01-01',
            'estimated_end_date' => '2026-12-31',
        ], (new StoreProjectRequest)->rules());

        $this->assertFalse($validator->fails());
    }

    public function test_update_project_allows_partial_update(): void
    {
        $validator = Validator::make(['name' => 'Novo Nome'], (new UpdateProjectRequest)->rules());

        $this->assertFalse($validator->fails());
    }

    public function test_update_project_validates_enum(): void
    {
        $validator = Validator::make(['status' => 'inválido'], (new UpdateProjectRequest)->rules());

        $this->assertTrue($validator->fails());
    }

    public function test_store_release_validates_required_version(): void
    {
        $validator = Validator::make([], (new StoreReleaseRequest)->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('version', $validator->errors()->toArray());
    }

    public function test_store_release_passes_with_valid_data(): void
    {
        $validator = Validator::make([
            'version' => '1.0.0',
            'status' => 'stable',
            'released_at' => '2026-01-01',
        ], (new StoreReleaseRequest)->rules());

        $this->assertFalse($validator->fails());
    }

    public function test_update_release_validates_enum(): void
    {
        $validator = Validator::make(['status' => 'bad'], (new UpdateReleaseRequest)->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('status', $validator->errors()->toArray());
    }

    public function test_store_environment_validates_required_name(): void
    {
        $validator = Validator::make([], (new StoreEnvironmentRequest)->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function test_store_environment_validates_url_and_enum(): void
    {
        $validator = Validator::make([
            'name' => 'Prod',
            'url' => 'not-a-url',
            'status' => 'nope',
        ], (new StoreEnvironmentRequest)->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('url', $validator->errors()->toArray());
        $this->assertArrayHasKey('status', $validator->errors()->toArray());
    }

    public function test_update_environment_validates_enum(): void
    {
        $validator = Validator::make(['status' => 'x'], (new UpdateEnvironmentRequest)->rules());

        $this->assertTrue($validator->fails());
    }

    public function test_store_api_endpoint_requires_existing_environment(): void
    {
        $validator = Validator::make([
            'environment_id' => 99999,
            'name' => 'Health',
            'method' => 'GET',
            'url' => '/health',
        ], (new StoreApiEndpointRequest)->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('environment_id', $validator->errors()->toArray());
    }

    public function test_store_api_endpoint_passes_with_valid_data(): void
    {
        $environment = Environment::factory()->create();

        $validator = Validator::make([
            'environment_id' => $environment->id,
            'name' => 'Health',
            'method' => 'GET',
            'url' => '/health',
            'status' => 'healthy',
        ], (new StoreApiEndpointRequest)->rules());

        $this->assertFalse($validator->fails());
    }

    public function test_store_api_endpoint_validates_method_enum(): void
    {
        $environment = Environment::factory()->create();

        $validator = Validator::make([
            'environment_id' => $environment->id,
            'name' => 'Health',
            'method' => 'FETCH',
            'url' => '/health',
        ], (new StoreApiEndpointRequest)->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('method', $validator->errors()->toArray());
    }

    public function test_update_api_endpoint_validates_enum(): void
    {
        $validator = Validator::make(['method' => 'FETCH'], (new UpdateApiEndpointRequest)->rules());

        $this->assertTrue($validator->fails());
    }

    public function test_store_team_member_requires_existing_user(): void
    {
        $validator = Validator::make([
            'user_id' => 99999,
            'role' => 'developer',
        ], (new StoreTeamMemberRequest)->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('user_id', $validator->errors()->toArray());
    }

    public function test_store_team_member_passes_with_valid_data(): void
    {
        $user = User::factory()->create();

        $validator = Validator::make([
            'user_id' => $user->id,
            'role' => 'developer',
        ], (new StoreTeamMemberRequest)->rules());

        $this->assertFalse($validator->fails());
    }

    public function test_store_team_member_validates_role_enum(): void
    {
        $user = User::factory()->create();

        $validator = Validator::make([
            'user_id' => $user->id,
            'role' => 'team_lead',
        ], (new StoreTeamMemberRequest)->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('role', $validator->errors()->toArray());
    }

    public function test_update_team_member_validates_role_enum(): void
    {
        $validator = Validator::make(['role' => 'team_lead'], (new UpdateTeamMemberRequest)->rules());

        $this->assertTrue($validator->fails());
    }
}
