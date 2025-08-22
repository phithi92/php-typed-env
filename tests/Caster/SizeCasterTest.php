<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\KeyRule;
use InvalidArgumentException;

final class SizeCasterTest extends TestCase
{
    public function testValidSizes(): void
    {
        $r = (new KeyRule('S'))->typeSize();
        self::assertSame(123, $r->apply('123'));
        self::assertSame(1024, $r->apply('1kb'));
        self::assertSame(2 * 1024 * 1024, $r->apply('2 MB'));
    }

    public function testInvalidSize(): void
    {
        $r = (new KeyRule('S'))->typeSize();
        $this->expectException(InvalidArgumentException::class);
        $r->apply('5xb');
    }
}
