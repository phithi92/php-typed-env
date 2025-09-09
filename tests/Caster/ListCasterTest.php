<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Caster\ListCaster;

final class ListCasterTest extends TestCase
{
    public function testListCasterAllowsEmptyOption(): void
    {
        $caster = new ListCaster(',', false);
        $this->assertSame(['a','','b'], $caster->cast('L', 'a, ,b'));
    }

    public function testListCasterFilterEmptyOption(): void
    {
        $caster = new ListCaster(',', true);
        $this->assertSame(['a','b'], $caster->cast('L', 'a, ,b'));
    }
}
