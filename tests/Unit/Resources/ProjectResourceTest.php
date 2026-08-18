<?php

namespace Tests\Unit\Resources;

use App\Enums\ProjectStatus;
use App\Http\Resources\ProjectResource;
use App\Models\ApiEndpoint;
use App\Models\Environment;
use App\Models\Project;
use App\Models\Release;
use App\Models\TeamMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_to_array_returns_expected_structure(): void
    {
        $project = Project::factory()->create([
            'name' => 'Projeto X',
            'description' => 'Desc',
            'primary_language' => 'PHP',
            'framework' => 'Laravel',
            'status' => ProjectStatus::Active,
            'start_date' => '2026-01-01',
            'estimated_end_date' => '2026-12-31',
            'git_repository' => 'https://github.com/x',
            'documentation_url' => 'https://docs.x.com',
        ]);

        $array = (new ProjectResource($project))->toArray(request());

        $this->assertSame($project->id, $array['id']);
        $this->assertSame('Projeto X', $array['name']);
        $this->assertSame('Desc', $array['description']);
        $this->assertSame('PHP', $array['primary_language']);
        $this->assertSame('Laravel', $array['framework']);
        $this->assertSame('active', $array['status']);
        $this->assertSame('Ativo', $array['status_label']);
        $this->assertSame('2026-01-01', $array['start_date']);
        $this->assertSame('2026-12-31', $array['estimated_end_date']);
        $this->assertSame('https://github.com/x', $array['git_repository']);
        $this->assertSame('https://docs.x.com', $array['documentation_url']);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertArrayHasKey('updated_at', $array);
    }

    public function test_relations_are_included_when_loaded(): void
    {
        $project = Project::factory()->create();
        $environment = Environment::factory()->create(['project_id' => $project->id]);
        $release = Release::factory()->create(['project_id' => $project->id]);
        $endpoint = ApiEndpoint::factory()->create([
            'project_id' => $project->id,
            'environment_id' => $environment->id,
        ]);
        $member = TeamMember::factory()->create(['project_id' => $project->id]);

        $project->load(['environments', 'releases', 'endpoints', 'teamMembers']);

        $array = (new ProjectResource($project))->toArray(request());

        $this->assertArrayHasKey('environments', $array);
        $this->assertArrayHasKey('releases', $array);
        $this->assertArrayHasKey('endpoints', $array);
        $this->assertArrayHasKey('team_members', $array);
        $this->assertCount(1, $array['environments']);
        $this->assertCount(1, $array['releases']);
        $this->assertCount(1, $array['endpoints']);
        $this->assertCount(1, $array['team_members']);
    }
}
