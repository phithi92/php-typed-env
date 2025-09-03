<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Types;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Types\FloatKeyRule;
use Phithi92\TypedEnv\Exception\CastException;
use Phithi92\TypedEnv\Exception\ConstraintException;

final class FloatKeyRuleTest extends TestCase
{
    public function testCastsAndAppliesMinMax(): void
    {
        $rule = new FloatKeyRule('FLOAT_KEY');
        $rule->min(0.1)->max(1.5);

        $this->assertSame(1.2, $rule->apply('1.2'));
    }

    public function testInvalidFloatThrowsCastExceptionWithMessage(): void
    {
        $this->expectException(CastException::class);
        $this->expectExceptionMessage("ENV FLOAT_KEY: 'not-a-float' is not a valid float");
        (new FloatKeyRule('FLOAT_KEY'))->apply('not-a-float');
    }

    public function testViolatesMaxThrowsConstraintExceptionWithMessage(): void
    {
        $rule = new FloatKeyRule('FLOAT_KEY');
        $rule->max(1.0);

        $this->expectException(ConstraintException::class);
        $this->expectExceptionMessage('ENV FLOAT_KEY: value 1.5 > max 1');
        $rule->apply('1.5');
    }
}
