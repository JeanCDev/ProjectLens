<?php

namespace Tests\Unit\Resources;

use App\Enums\EnvironmentStatus;
use App\Http\Resources\EnvironmentResource;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnvironmentResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_to_array_returns_expected_structure(): void
    {
        $environment = Environment::factory()->create([
            'name' => 'Production',
            'url' => 'https://prod.example.com',
            'database' => 'MySQL 8.0',
            'version' => '2.0.0',
            'status' => EnvironmentStatus::Active,
        ]);

        $array = (new EnvironmentResource($environment))->toArray(request());

        $this->assertSame($environment->id, $array['id']);
        $this->assertSame($environment->project_id, $array['project_id']);
        $this->assertSame('Production', $array['name']);
        $this->assertSame('https://prod.example.com', $array['url']);
        $this->assertSame('MySQL 8.0', $array['database']);
        $this->assertSame('2.0.0', $array['version']);
        $this->assertSame('active', $array['status']);
        $this->assertSame('Ativo', $array['status_label']);
    }
}
