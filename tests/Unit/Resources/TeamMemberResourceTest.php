<?php

namespace Tests\Unit\Resources;

use App\Enums\TeamMemberRole;
use App\Http\Resources\TeamMemberResource;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Resources\MissingValue;
use Tests\TestCase;

class TeamMemberResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_to_array_returns_expected_structure(): void
    {
        $user = User::factory()->create(['name' => 'João', 'email' => 'joao@example.com']);
        $member = TeamMember::factory()->create([
            'user_id' => $user->id,
            'role' => TeamMemberRole::Developer,
        ]);
        $member->load('user');

        $array = (new TeamMemberResource($member))->toArray(request());

        $this->assertSame($member->id, $array['id']);
        $this->assertSame($member->project_id, $array['project_id']);
        $this->assertSame($user->id, $array['user_id']);
        $this->assertSame('developer', $array['role']);
        $this->assertSame('Desenvolvedor', $array['role_label']);
        $this->assertSame('João', $array['user']['name']);
        $this->assertSame('joao@example.com', $array['user']['email']);
    }

    public function test_user_is_not_included_when_not_loaded(): void
    {
        $member = TeamMember::factory()->create();

        $array = (new TeamMemberResource($member))->toArray(request());

        $this->assertArrayHasKey('user', $array);
        $this->assertInstanceOf(MissingValue::class, $array['user']['id']);
    }
}
