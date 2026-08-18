<?php

namespace Tests\Unit\Enums;

use App\Enums\ProjectStatus;
use PHPUnit\Framework\TestCase;

class ProjectStatusTest extends TestCase
{
    public function test_cases_have_expected_values(): void
    {
        $this->assertSame('planning', ProjectStatus::Planning->value);
        $this->assertSame('active', ProjectStatus::Active->value);
        $this->assertSame('on_hold', ProjectStatus::OnHold->value);
        $this->assertSame('completed', ProjectStatus::Completed->value);
        $this->assertSame('archived', ProjectStatus::Archived->value);
    }

    public function test_labels(): void
    {
        $this->assertSame('Planejamento', ProjectStatus::Planning->label());
        $this->assertSame('Ativo', ProjectStatus::Active->label());
        $this->assertSame('Em Pausa', ProjectStatus::OnHold->label());
        $this->assertSame('Concluído', ProjectStatus::Completed->label());
        $this->assertSame('Arquivado', ProjectStatus::Archived->label());
    }
}
