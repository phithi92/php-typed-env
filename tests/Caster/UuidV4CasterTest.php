<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\KeyRule;
use InvalidArgumentException;

final class UuidV4CasterTest extends TestCase
{
    public function testValidUuidV4(): void
    {
        $r = (new KeyRule('ID'))->typeUuidV4();
        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440000',
            $r->apply('550e8400-e29b-41d4-a716-446655440000')
        );
    }

    public function testRejectNonV4Uuid(): void
    {
        $r = (new KeyRule('ID'))->typeUuidV4();
        $this->expectException(InvalidArgumentException::class);
        $r->apply('550e8400-e29b-11d4-a716-446655440000'); // v1
    }
}
