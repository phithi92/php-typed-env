<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Types;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Types\BoolKeyRule;
use Phithi92\TypedEnv\Exception\CastException;

final class BoolKeyRuleTest extends TestCase
{
    public function testCastsTrueAndFalse(): void
    {
        $rule = new BoolKeyRule('BOOL_KEY');
        $this->assertTrue($rule->apply('true'));
        $this->assertFalse($rule->apply('false'));
    }

    public function testInvalidBoolThrowsCastException(): void
    {
        $this->expectException(CastException::class);
        $this->expectExceptionMessage("ENV BOOL_KEY: 'maybe' is not a valid bool");

        (new BoolKeyRule('BOOL_KEY'))->apply('maybe');
    }
}
