<?php

namespace Tests\Unit\Models;

use App\Enums\ProjectStatus;
use App\Models\ApiEndpoint;
use App\Models\Environment;
use App\Models\Project;
use App\Models\Release;
use App\Models\TeamMember;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_has_fillable_attributes(): void
    {
        $project = Project::create([
            'name' => 'Teste',
            'status' => 'active',
        ]);

        $this->assertSame('Teste', $project->name);
        $this->assertInstanceOf(ProjectStatus::class, $project->status);
        $this->assertSame('active', $project->status->value);
    }

    public function test_status_is_cast_to_enum(): void
    {
        $project = Project::factory()->create(['status' => ProjectStatus::OnHold]);

        $this->assertInstanceOf(ProjectStatus::class, $project->status);
        $this->assertSame(ProjectStatus::OnHold, $project->status);
    }

    public function test_dates_are_cast(): void
    {
        $project = Project::factory()->create([
            'start_date' => '2026-01-01',
            'estimated_end_date' => '2026-12-31',
        ]);

        $this->assertInstanceOf(CarbonInterface::class, $project->start_date);
        $this->assertInstanceOf(CarbonInterface::class, $project->estimated_end_date);
    }

    public function test_has_environments_relation(): void
    {
        $project = Project::factory()->create();
        $environment = Environment::factory()->create(['project_id' => $project->id]);

        $this->assertTrue($project->environments->contains($environment));
    }

    public function test_has_releases_relation(): void
    {
        $project = Project::factory()->create();
        $release = Release::factory()->create(['project_id' => $project->id]);

        $this->assertTrue($project->releases->contains($release));
    }

    public function test_has_endpoints_relation(): void
    {
        $project = Project::factory()->create();
        $environment = Environment::factory()->create(['project_id' => $project->id]);
        $endpoint = ApiEndpoint::factory()->create([
            'project_id' => $project->id,
            'environment_id' => $environment->id,
        ]);

        $this->assertTrue($project->endpoints->contains($endpoint));
    }

    public function test_has_team_members_relation(): void
    {
        $project = Project::factory()->create();
        $user = User::factory()->create();
        $member = TeamMember::factory()->create([
            'project_id' => $project->id,
            'user_id' => $user->id,
        ]);

        $this->assertTrue($project->teamMembers->contains($member));
    }
}
