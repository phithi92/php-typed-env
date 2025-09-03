<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Types;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Types\ListKeyRule;

final class ListKeyRuleTest extends TestCase
{
    public function testAllowedValuesAndNotEmpty(): void
    {
        $rule = (new ListKeyRule('ROLES', ','))
            ->allowedValues(['admin','user','guest'])
            ->assertValuesNotEmpty();
        $rule->apply('admin,user');

        $this->assertSame(['admin','user'], $rule->apply('admin,user'));
    }
}
