<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Caster\ArrayCaster;

final class ArrayCasterTest extends TestCase
{
    public function testArrayCasterSplitsAndTrims(): void
    {
        $caster = new ArrayCaster(',', false);
        $out = $caster->cast('LIST', '  a , b ,  c,  ');
        $this->assertSame(['a','b','c',''], $out);
    }

    public function testArrayCasterSplitsAndTrimsAndFilterEmpty(): void
    {
        $caster = new ArrayCaster(',', true);
        $out = $caster->cast('LIST', '  a , b ,  c,  ');
        $this->assertSame(['a','b','c'], $out);
    }
}
