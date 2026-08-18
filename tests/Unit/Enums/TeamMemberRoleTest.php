<?php

namespace Tests\Unit\Enums;

use App\Enums\TeamMemberRole;
use PHPUnit\Framework\TestCase;

class TeamMemberRoleTest extends TestCase
{
    public function test_cases_have_expected_values(): void
    {
        $this->assertSame('admin', TeamMemberRole::Admin->value);
        $this->assertSame('developer', TeamMemberRole::Developer->value);
        $this->assertSame('designer', TeamMemberRole::Designer->value);
        $this->assertSame('devops', TeamMemberRole::DevOps->value);
        $this->assertSame('manager', TeamMemberRole::Manager->value);
        $this->assertSame('viewer', TeamMemberRole::Viewer->value);
    }

    public function test_labels(): void
    {
        $this->assertSame('Administrador', TeamMemberRole::Admin->label());
        $this->assertSame('Desenvolvedor', TeamMemberRole::Developer->label());
        $this->assertSame('Designer', TeamMemberRole::Designer->label());
        $this->assertSame('DevOps', TeamMemberRole::DevOps->label());
        $this->assertSame('Gerente', TeamMemberRole::Manager->label());
        $this->assertSame('Visualizador', TeamMemberRole::Viewer->label());
    }
}
