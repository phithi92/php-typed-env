<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Types;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Types\LocaleKeyRule;
use Phithi92\TypedEnv\Exception\ConstraintException;

final class LocaleKeyRuleTest extends TestCase
{
    public function testAllowedLocales(): void
    {
        $rule = new LocaleKeyRule('LOCALE');
        $rule->allowed(['en_US','de_DE']);

        $this->assertSame('de_DE', $rule->apply('de_DE'));

        $this->expectException(ConstraintException::class);
        $rule->apply('fr_FR');
    }
}
