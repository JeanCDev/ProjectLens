<?php

namespace Tests\Unit\Models;

use App\Enums\EndpointStatus;
use App\Enums\HttpMethod;
use App\Models\ApiEndpoint;
use App\Models\Environment;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiEndpointModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_method_and_status_are_cast_to_enums(): void
    {
        $endpoint = ApiEndpoint::factory()->create([
            'method' => HttpMethod::Post,
            'status' => EndpointStatus::Degraded,
        ]);

        $this->assertInstanceOf(HttpMethod::class, $endpoint->method);
        $this->assertInstanceOf(EndpointStatus::class, $endpoint->status);
        $this->assertSame(HttpMethod::Post, $endpoint->method);
        $this->assertSame(EndpointStatus::Degraded, $endpoint->status);
    }

    public function test_response_time_is_cast_to_integer(): void
    {
        $endpoint = ApiEndpoint::factory()->create(['response_time_ms' => '150']);

        $this->assertIsInt($endpoint->response_time_ms);
        $this->assertSame(150, $endpoint->response_time_ms);
    }

    public function test_belongs_to_project(): void
    {
        $project = Project::factory()->create();
        $endpoint = ApiEndpoint::factory()->create(['project_id' => $project->id]);

        $this->assertTrue($endpoint->project->is($project));
    }

    public function test_belongs_to_environment(): void
    {
        $environment = Environment::factory()->create();
        $endpoint = ApiEndpoint::factory()->create(['environment_id' => $environment->id]);

        $this->assertTrue($endpoint->environment->is($environment));
    }
}
