<?php

namespace Tests\Unit\Resources;

use App\Enums\ReleaseStatus;
use App\Http\Resources\ReleaseResource;
use App\Models\Release;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReleaseResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_to_array_returns_expected_structure(): void
    {
        $release = Release::factory()->create([
            'version' => '1.2.3',
            'changelog' => 'Mudanças',
            'released_at' => '2026-05-01',
            'status' => ReleaseStatus::Stable,
        ]);

        $array = (new ReleaseResource($release))->toArray(request());

        $this->assertSame($release->id, $array['id']);
        $this->assertSame($release->project_id, $array['project_id']);
        $this->assertSame('1.2.3', $array['version']);
        $this->assertSame('Mudanças', $array['changelog']);
        $this->assertSame('2026-05-01', $array['released_at']);
        $this->assertSame('stable', $array['status']);
        $this->assertSame('Estável', $array['status_label']);
    }
}
