<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Types;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Types\ArrayKeyRule;
use Phithi92\TypedEnv\Exception\CastException;
use Phithi92\TypedEnv\Exception\ConstraintException;

final class ArrayKeyRuleTest extends TestCase
{
    public function testValidArrayItemsRange(): void
    {
        $rule = new ArrayKeyRule('HOSTS', ',');
        $rule->rangeItems(1, 3);

        $this->assertSame(['a','b'], $rule->apply('a,b'));
    }

    public function testInvalidArrayCastEmptyString(): void
    {
        $rule = new ArrayKeyRule('HOSTS', ',');

        $this->expectException(ConstraintException::class);
        $this->expectExceptionMessage("ENV HOSTS is required but missing");
        $this->assertEquals([], $rule->apply(''));

        $this->expectException(CastException::class);
        $this->expectExceptionMessage("ENV HOSTS: '' is not a valid array");
    }
}
