<?php

namespace Tests\Unit\Resources;

use App\Enums\EndpointStatus;
use App\Http\Resources\ApiEndpointResource;
use App\Models\ApiEndpoint;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiEndpointResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_to_array_returns_expected_structure(): void
    {
        $environment = Environment::factory()->create();
        $endpoint = ApiEndpoint::factory()->create([
            'environment_id' => $environment->id,
            'name' => 'Health',
            'method' => 'GET',
            'url' => '/health',
            'status' => EndpointStatus::Healthy,
            'last_checked_at' => '2026-08-18 10:00:00',
            'response_time_ms' => 120,
        ]);
        $endpoint->load('environment');

        $array = (new ApiEndpointResource($endpoint))->toArray(request());

        $this->assertSame($endpoint->id, $array['id']);
        $this->assertSame($endpoint->project_id, $array['project_id']);
        $this->assertSame($environment->id, $array['environment_id']);
        $this->assertSame('Health', $array['name']);
        $this->assertSame('GET', $array['method']);
        $this->assertSame('/health', $array['url']);
        $this->assertSame('healthy', $array['status']);
        $this->assertSame('Saudável', $array['status_label']);
        $this->assertSame(120, $array['response_time_ms']);
        $this->assertArrayHasKey('environment', $array);
        $this->assertSame($environment->id, $array['environment']['id']);
    }
}
