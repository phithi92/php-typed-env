<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\KeyRule;
use Phithi92\TypedEnv\Exception\CastException;
use stdClass;

final class JsonCasterTest extends TestCase
{
    public function testValidJsonAssoc(): void
    {
        $r = (new KeyRule('J'))->typeJson(true);
        self::assertSame(['a' => 1], $r->apply('{"a":1}'));
    }

    public function testValidJsonObject(): void
    {
        $r = (new KeyRule('J'))->typeJson(false);
        $result = $r->apply('{"a":1}');
        self::assertInstanceOf(stdClass::class, $result);
        self::assertSame(1, $result->a);
    }

    public function testInvalidJson(): void
    {
        $r = (new KeyRule('J'))->typeJson();
        $this->expectException(CastException::class);
        $r->apply('{');
    }
}
