<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Types;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Types\VersionKeyRule;
use Phithi92\TypedEnv\Exception\CastException;

final class VersionKeyRuleTest extends TestCase
{
    public function testValidVersionRange(): void
    {
        $rule = new VersionKeyRule('APP_VER');
        $rule->rangeVersion('1.2.0', '2.0.0');

        $this->assertSame('1.5.0', $rule->apply('1.5.0'));
    }

    public function testInvalidVersionCast(): void
    {
        $this->expectException(CastException::class);
        $this->expectExceptionMessage(
            "Environment variable 'APP_VER' must be a valid semantic version (compliant with SemVer 2.0.0)."
        );

        (new VersionKeyRule('APP_VER'))->apply('v1');
    }
}
