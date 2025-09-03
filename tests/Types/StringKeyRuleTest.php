<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Types;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Types\StringKeyRule;
use Phithi92\TypedEnv\Exception\ConstraintException;

final class StringKeyRuleTest extends TestCase
{
    public function testCastsStringAndEnum(): void
    {
        $rule = new StringKeyRule('STR_KEY');
        $this->assertSame('Alice', $rule->apply('Alice'));

        $rule = new StringKeyRule('ENV');
        $rule->enum(['dev','prod']);
        $this->assertSame('dev', $rule->apply('dev'));

        $this->expectException(ConstraintException::class);
        $rule->apply('staging');
    }

    public function testPatternConstraintPassesAndFails(): void
    {
        $rule = new StringKeyRule('USER');
        $rule->pattern('/^[A-Z][a-z]+$/');

        $this->assertSame('Alice', $rule->apply('Alice'));

        $this->expectException(ConstraintException::class);
        $rule->apply('alice');
    }
}
