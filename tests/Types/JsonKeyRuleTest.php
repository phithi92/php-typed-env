<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Types;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Types\JsonKeyRule;
use Phithi92\TypedEnv\Exception\CastException;

final class JsonKeyRuleTest extends TestCase
{
    public function testValidJsonArrayRange(): void
    {
        $rule = new JsonKeyRule('SETTINGS', true);
        $rule->rangeItems(1, 3);

        $this->assertSame(['a','b'], $rule->apply('["a","b"]'));
    }

    public function testInvalidJsonCast(): void
    {
        $this->expectException(CastException::class);

        (new JsonKeyRule('SETTINGS', true))->apply('not json');
    }
}
