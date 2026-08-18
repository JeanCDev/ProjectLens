<?php

namespace Tests\Unit\Models;

use App\Enums\EnvironmentStatus;
use App\Models\ApiEndpoint;
use App\Models\Environment;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnvironmentModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_is_cast_to_enum(): void
    {
        $environment = Environment::factory()->create(['status' => EnvironmentStatus::Maintenance]);

        $this->assertInstanceOf(EnvironmentStatus::class, $environment->status);
        $this->assertSame(EnvironmentStatus::Maintenance, $environment->status);
    }

    public function test_belongs_to_project(): void
    {
        $project = Project::factory()->create();
        $environment = Environment::factory()->create(['project_id' => $project->id]);

        $this->assertTrue($environment->project->is($project));
    }

    public function test_has_endpoints_relation(): void
    {
        $environment = Environment::factory()->create();
        $endpoint = ApiEndpoint::factory()->create(['environment_id' => $environment->id]);

        $this->assertTrue($environment->endpoints->contains($endpoint));
    }
}
