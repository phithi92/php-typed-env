<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Caster\PathCaster;
use Phithi92\TypedEnv\Exception\CastException;

final class PathCasterTest extends TestCase
{
    public function testPathCasterResolveRealpath(): void
    {
        $tmp = sys_get_temp_dir() . '/typedenv_test_' . uniqid();
        file_put_contents($tmp, 'x');
        try {
            $caster = new PathCaster(true);
            $resolved = $caster->cast('P', $tmp);
            $this->assertSame(realpath($tmp), $resolved);
            $this->expectException(CastException::class);
            $caster->cast('P', $tmp . '_missing');
        } finally {
            @unlink($tmp);
        }
    }
}
