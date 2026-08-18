<?php

namespace Tests\Unit\Enums;

use App\Enums\EndpointStatus;
use PHPUnit\Framework\TestCase;

class EndpointStatusTest extends TestCase
{
    public function test_cases_have_expected_values(): void
    {
        $this->assertSame('healthy', EndpointStatus::Healthy->value);
        $this->assertSame('degraded', EndpointStatus::Degraded->value);
        $this->assertSame('down', EndpointStatus::Down->value);
        $this->assertSame('unknown', EndpointStatus::Unknown->value);
    }

    public function test_labels(): void
    {
        $this->assertSame('Saudável', EndpointStatus::Healthy->label());
        $this->assertSame('Degradado', EndpointStatus::Degraded->label());
        $this->assertSame('Fora do Ar', EndpointStatus::Down->label());
        $this->assertSame('Desconhecido', EndpointStatus::Unknown->label());
    }
}
