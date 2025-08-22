<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\KeyRule;

final class ListCasterTest extends TestCase
{
    public function testListTrimAndEmpty(): void
    {
        $r = (new KeyRule('L'))->typeList(',', false);
        self::assertSame(['a','b','c'], $r->apply(' a, b , , c '));

        $r2 = (new KeyRule('L'))->typeList(',', true);
        self::assertSame(['a','b','','c'], $r2->apply(' a, b , , c '));
    }

    public function testCustomDelimiter(): void
    {
        $r = (new KeyRule('L'))->typeList(';');
        self::assertSame(['a','b','c'], $r->apply('a; b ;c'));
    }
}
