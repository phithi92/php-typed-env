<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Caster\RegexCaster;
use Phithi92\TypedEnv\Exception\CastException;

final class RegexCasterTest extends TestCase
{
    public function testRegexCaster(): void
    {
        $caster = new RegexCaster('/^\d{3}$/');
        $this->assertSame('123', $caster->cast('R', '123'));
        $this->expectException(CastException::class);
        $caster->cast('R', '12a');
    }
}
