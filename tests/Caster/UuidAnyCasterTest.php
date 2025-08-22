<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\KeyRule;
use Phithi92\TypedEnv\Exception\CastException;

final class UuidAnyCasterTest extends TestCase
{
    public function testValidUuid(): void
    {
        $r = (new KeyRule('ID'))->typeUuidAny();
        self::assertSame(
            '550e8400-e29b-11d4-a716-446655440000',
            $r->apply('550e8400-e29b-11d4-a716-446655440000')
        );
    }

    public function testInvalidUuid(): void
    {
        $r = (new KeyRule('ID'))->typeUuidAny();
        $this->expectException(CastException::class);
        $r->apply('not-a-uuid');
    }
}
