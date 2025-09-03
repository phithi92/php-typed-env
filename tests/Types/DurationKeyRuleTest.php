<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Types;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Types\DurationKeyRule;
use Phithi92\TypedEnv\Exception\CastException;

final class DurationKeyRuleTest extends TestCase
{
    public function testValidDurationSecondsRange(): void
    {
        $rule = new DurationKeyRule('TIMEOUT');
        $rule->rangeSeconds(1, 120);

        $this->assertSame(30, $rule->apply('30s'));
    }

    public function testInvalidDurationCast(): void
    {
        $this->expectException(CastException::class);
        $this->expectExceptionMessage("ENV TIMEOUT: 'banana' is not a valid duration");

        (new DurationKeyRule('TIMEOUT'))->apply('banana');
    }
}
