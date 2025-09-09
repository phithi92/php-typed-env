<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Caster\Base64Caster;
use Phithi92\TypedEnv\Exception\CastException;

final class Base64CasterTest extends TestCase
{
    public function testBase64ValidAndInvalid(): void
    {
        $caster = new Base64Caster();
        $this->assertSame('SGVsbG8=', $caster->cast('B64', 'SGVsbG8='));
        $this->expectException(CastException::class);
        $caster->cast('B64', 'not base64!');
    }
}
