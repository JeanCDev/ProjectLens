<?php

namespace Tests\Unit\Enums;

use App\Enums\ReleaseStatus;
use PHPUnit\Framework\TestCase;

class ReleaseStatusTest extends TestCase
{
    public function test_cases_have_expected_values(): void
    {
        $this->assertSame('draft', ReleaseStatus::Draft->value);
        $this->assertSame('pre_release', ReleaseStatus::PreRelease->value);
        $this->assertSame('stable', ReleaseStatus::Stable->value);
        $this->assertSame('deprecated', ReleaseStatus::Deprecated->value);
    }

    public function test_labels(): void
    {
        $this->assertSame('Rascunho', ReleaseStatus::Draft->label());
        $this->assertSame('Pré-lançamento', ReleaseStatus::PreRelease->label());
        $this->assertSame('Estável', ReleaseStatus::Stable->label());
        $this->assertSame('Descontinuado', ReleaseStatus::Deprecated->label());
    }
}
