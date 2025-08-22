<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Caster\ListCaster;
use Phithi92\TypedEnv\Exception\CastException;

final class ListCasterTest extends TestCase
{
    public function testThrowsExceptionWhenDelimiterIsEmpty(): void
    {
        $this->expectException(CastException::class);
        $this->expectExceptionMessage('Delimiter must not be empty');

        new ListCaster('');
    }

    public function testConstructorAcceptsValidDelimiter(): void
    {
        $caster = new ListCaster(';');
        $result = $caster->cast('KEY', 'a;b;c');

        $this->assertSame(['a', 'b', 'c'], $result);
    }

    public function testDefaultAllowEmptyIsFalse(): void
    {
        $caster = new ListCaster(',');
        $result = $caster->cast('KEY', 'a,,b');

        // empty values removed
        $this->assertSame(['a', 'b'], $result);
    }

    public function testAllowEmptyTrueKeepsEmptyStrings(): void
    {
        $caster = new ListCaster(',', true);
        $result = $caster->cast('KEY', 'a,,b');

        // empty values preserved
        $this->assertSame(['a', '', 'b'], $result);
    }

    public function testTrimsWhitespaceFromValues(): void
    {
        $caster = new ListCaster();
        $result = $caster->cast('KEY', '  x ,  y  , z ');

        $this->assertSame(['x', 'y', 'z'], $result);
    }

    public function testCustomDelimiterAndAllowEmpty(): void
    {
        $caster = new ListCaster('|', true);
        $result = $caster->cast('KEY', 'a||b| |c');

        $this->assertSame(['a', '', 'b', '', 'c'], $result);
    }
}
