<?php

namespace Tests\Unit\Models;

use App\Enums\ReleaseStatus;
use App\Models\Project;
use App\Models\Release;
use Carbon\CarbonInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReleaseModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_is_cast_to_enum(): void
    {
        $release = Release::factory()->create(['status' => ReleaseStatus::Stable]);

        $this->assertInstanceOf(ReleaseStatus::class, $release->status);
        $this->assertSame(ReleaseStatus::Stable, $release->status);
    }

    public function test_released_at_is_cast(): void
    {
        $release = Release::factory()->create(['released_at' => '2026-01-15']);

        $this->assertInstanceOf(CarbonInterface::class, $release->released_at);
    }

    public function test_belongs_to_project(): void
    {
        $project = Project::factory()->create();
        $release = Release::factory()->create(['project_id' => $project->id]);

        $this->assertTrue($release->project->is($project));
    }
}
