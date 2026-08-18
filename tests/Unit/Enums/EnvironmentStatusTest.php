<?php

namespace Tests\Unit\Enums;

use App\Enums\EnvironmentStatus;
use PHPUnit\Framework\TestCase;

class EnvironmentStatusTest extends TestCase
{
    public function test_cases_have_expected_values(): void
    {
        $this->assertSame('active', EnvironmentStatus::Active->value);
        $this->assertSame('inactive', EnvironmentStatus::Inactive->value);
        $this->assertSame('maintenance', EnvironmentStatus::Maintenance->value);
    }

    public function test_labels(): void
    {
        $this->assertSame('Ativo', EnvironmentStatus::Active->label());
        $this->assertSame('Inativo', EnvironmentStatus::Inactive->label());
        $this->assertSame('Manutenção', EnvironmentStatus::Maintenance->label());
    }
}
