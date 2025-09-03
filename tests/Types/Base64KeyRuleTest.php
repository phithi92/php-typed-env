<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Types;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Types\Base64KeyRule;
use Phithi92\TypedEnv\Exception\CastException;

final class Base64KeyRuleTest extends TestCase
{
    public function testValidBase64LengthRange(): void
    {
        $rule = new Base64KeyRule('TOKEN_B64');
        $rule->rangeLength(4, 16);

        $this->assertSame('Zm9v', $rule->apply('Zm9v'));
    }

    public function testInvalidBase64Cast(): void
    {
        $notb64 = '@@';
        $this->expectException(CastException::class);
        $this->expectExceptionMessage("Environment variable 'TOKEN_B64' must be a valid Base64 encoded string.");

        (new Base64KeyRule('TOKEN_B64'))->apply($notb64);
    }
}
