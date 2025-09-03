<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Types;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Types\ChmodKeyRule;
use Phithi92\TypedEnv\Exception\CastException;
use Phithi92\TypedEnv\Exception\ConstraintException;

final class ChmodKeyRuleTest extends TestCase
{
    public function testValidModeRange(): void
    {
        $rule = new ChmodKeyRule('MODE');
        $rule->range(0o600, 0o777);

        // 0644 mit führender 0 → Oktal 0644 == 420
        $this->assertSame(0o644, $rule->apply('0644'));
    }

    public function testBoundaryInclusiveMin(): void
    {
        $rule = (new ChmodKeyRule('MODE'))->min(0o600);
        $this->assertSame(0o600, $rule->apply('0600')); // min ist erlaubt (inklusive)
    }

    public function testBoundaryInclusiveMax(): void
    {
        $rule = (new ChmodKeyRule('MODE'))->max(0o777);
        $this->assertSame(0o777, $rule->apply('0777')); // max ist erlaubt (inklusive)
    }

    public function testBelowMinFails(): void
    {
        $this->expectException(CastException::class);
        (new ChmodKeyRule('MODE'))
            ->min(0o640)
            ->apply('0639'); // 0639 ist < 0640 (Oktal)
    }

    public function testAboveMaxFails(): void
    {
        $this->expectException(ConstraintException::class);
        (new ChmodKeyRule('MODE'))
            ->max(0o755)
            ->apply('0756'); // 0756 ist > 0755 (Oktal)
    }

    public function testAcceptsThreeOrFourOctalDigits(): void
    {
        $rule = new ChmodKeyRule('MODE');
        // 3-stellig ohne führende 0
        $this->assertSame(0o644, $rule->apply('644'));
        // 4-stellig mit führender 0
        $this->assertSame(0o755, $rule->apply('0755'));
    }

    public function testRejectsNonOctalOrWrongLength(): void
    {
        $rule = new ChmodKeyRule('MODE');

        // Buchstaben → ungültig
        $this->expectException(CastException::class);
        $rule->apply('not-octal');
    }

    public function testRejectsDigitEightOrNine(): void
    {
        $rule = new ChmodKeyRule('MODE');

        // 8 ist keine Oktal-Ziffer
        $this->expectException(CastException::class);
        $rule->apply('0688');
    }

    public function testRejectsMinusSign(): void
    {
        $rule = new ChmodKeyRule('MODE');

        // Minuszeichen ist nicht erlaubt
        $this->expectException(CastException::class);
        $rule->apply('-644');
    }

    public function testRejectsTooManyDigits(): void
    {
        $rule = new ChmodKeyRule('MODE');

        // 5 Ziffern → Regex verlangt 3 oder 4
        $this->expectException(CastException::class);
        $rule->apply('00755'); // fünfstellig
    }

    public function testTrimsWhitespace(): void
    {
        $rule = new ChmodKeyRule('MODE');
        // ChmodCaster trimmt den Rohwert → sollte erfolgreich sein
        $this->assertSame(0o640, $rule->apply(' 0640  '));
    }

    public function testInvalidModeCast(): void
    {
        $this->expectException(CastException::class);
        (new ChmodKeyRule('MODE'))->apply('not-octal');
    }
}
