<?php

namespace Tests\Unit\Enums;

use App\Enums\HttpMethod;
use PHPUnit\Framework\TestCase;

class HttpMethodTest extends TestCase
{
    public function test_cases_have_expected_values(): void
    {
        $this->assertSame('GET', HttpMethod::Get->value);
        $this->assertSame('POST', HttpMethod::Post->value);
        $this->assertSame('PUT', HttpMethod::Put->value);
        $this->assertSame('PATCH', HttpMethod::Patch->value);
        $this->assertSame('DELETE', HttpMethod::Delete->value);
        $this->assertSame('OPTIONS', HttpMethod::Options->value);
        $this->assertSame('HEAD', HttpMethod::Head->value);
    }
}
