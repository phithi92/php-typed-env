<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Types;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Types\PathKeyRule;
use Phithi92\TypedEnv\Exception\CastException;
use Phithi92\TypedEnv\Exception\ConstraintException;

final class PathKeyRuleTest extends TestCase
{
    public function testExistsAndWritableOnTempFile(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'tpe');
        self::assertNotFalse($tmp);
        chmod($tmp, 0600);

        $rule = new PathKeyRule('CONF_PATH', true);
        $rule->existsAndWritable();

        $this->assertSame($tmp, $rule->apply($tmp));

        @unlink($tmp);
    }

    public function testInvalidPathCast(): void
    {
        $this->expectException(ConstraintException::class);
        (new PathKeyRule('CONF_PATH', true))->apply('');
    }
}
