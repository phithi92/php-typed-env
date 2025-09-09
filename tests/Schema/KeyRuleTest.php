<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Schema;

use ArrayObject;
use Phithi92\TypedEnv\Contracts\CasterInterface;
use Phithi92\TypedEnv\Contracts\ConstraintInterface;
use Phithi92\TypedEnv\Exception\ConstraintException;
use Phithi92\TypedEnv\Schema\KeyRule;
use PHPUnit\Framework\TestCase;

final class KeyRuleTest extends TestCase
{
    private function makeRule(string $key = 'FOO'): KeyRule
    {
        // KeyRule ist abstract, hat aber keine abstract methods -> anonyme Subklasse genügt.
        return new class ($key) extends KeyRule {
        };
    }

    public function testRequiredMissingThrowsConstraintException(): void
    {
        $rule = $this->makeRule('REQUIRED_KEY');

        $this->expectException(ConstraintException::class);
        $this->expectExceptionMessage('REQUIRED_KEY');

        $rule->apply(null);
    }

    public function testOptionalMissingReturnsNull(): void
    {
        $rule = $this->makeRule('OPTIONAL_KEY')->optional();

        $this->assertNull($rule->apply(null));
    }

    public function testDefaultValueIsReturnedWhenMissingAndMarksOptional(): void
    {
        $rule = $this->makeRule('DEFAULTED')->default('fallback');

        $this->assertSame('fallback', $rule->apply(null));
        // Absicherung der Meta-Flags
        $this->assertFalse($rule->isRequired());
        $this->assertTrue($rule->hasDefault());
        $this->assertSame('fallback', $rule->getDefault());
        $this->assertSame('DEFAULTED', $rule->key());
    }

    public function testStringInputUsesSetCasterAndRunsConstraintsInOrder(): void
    {
        $rule = $this->makeRule('STRINGY');

        // Mock Caster: '123' -> 123 (int)
        $caster = $this->createMock(CasterInterface::class);
        $caster->expects($this->once())
            ->method('cast')
            ->with('STRINGY', '123')
            ->willReturn(123);
        $rule->setCaster($caster);

        // Constraint 1: +1
        $c1 = $this->createMock(ConstraintInterface::class);
        $c1->expects($this->once())
            ->method('assert')
            ->with('STRINGY', 123)
            ->willReturn(124);

        // Constraint 2: *2
        $c2 = $this->createMock(ConstraintInterface::class);
        $c2->expects($this->once())
            ->method('assert')
            ->with('STRINGY', 124)
            ->willReturn(248);

        $rule->addConstraint($c1)->addConstraint($c2);

        $out = $rule->apply('123');
        $this->assertSame(248, $out);
    }

    public function testZeroLikeValuesAreNotMissing(): void
    {
        $rule = $this->makeRule('ZEROISH')->optional(); // optional nur damit kein required-Fehler kommt

        // Setze einen Caster, um sicherzugehen, dass bei String gecastet wird.
        $caster = $this->createMock(CasterInterface::class);
        $caster->expects($this->once())
            ->method('cast')
            ->with('ZEROISH', '0')
            ->willReturn(0);
        $rule->setCaster($caster);

        // Keine Constraints nötig; wichtig ist, dass '0' nicht als "missing" gilt.
        $this->assertSame(0, $rule->apply('0'));

        // Für false (bool) -> kein String, kein Caster, kein Missing
        $this->assertFalse($rule->apply(false));
    }

    public function testEmptyStringIsMissingAndTriggersRequiredError(): void
    {
        $rule = $this->makeRule('EMPTY_REQUIRED');

        $this->expectException(ConstraintException::class);
        $this->expectExceptionMessage('EMPTY_REQUIRED');

        $rule->apply('');
    }

    public function testEmptyArrayIsMissingAndUsesDefault(): void
    {
        $rule = $this->makeRule('EMPTY_ARRAY')->default(['x' => 1]);

        $this->assertSame(['x' => 1], $rule->apply([]));
    }

    public function testEmptyCountableIsMissingAndOptionalReturnsNull(): void
    {
        $rule = $this->makeRule('EMPTY_COUNTABLE')->optional();
        $empty = new ArrayObject(); // Countable, count() === 0

        $this->assertNull($rule->apply($empty));
    }

    public function testConstraintExceptionBubblesUp(): void
    {
        $rule = $this->makeRule('BOOM');

        $constraint = $this->createMock(ConstraintInterface::class);
        $constraint->expects($this->once())
            ->method('assert')
            ->willThrowException(new ConstraintException('nope'));

        $rule->addConstraint($constraint);

        $this->expectException(ConstraintException::class);
        $this->expectExceptionMessage('nope');

        $rule->apply('anything');
    }

    public function testDefaultIsNotUsedWhenValuePresentEvenIfEmptyStringCastsToValue(): void
    {
        // Hier prüfen wir, dass Default wirklich nur bei "missing" greift.
        // Da "" als missing gilt, simulieren wir stattdessen einen nicht-leeren String.
        $rule = $this->makeRule('WITH_DEFAULT')->default('fallback');

        $caster = $this->createMock(CasterInterface::class);
        $caster->expects($this->once())
            ->method('cast')
            ->with('WITH_DEFAULT', 'value')
            ->willReturn('CASTED');
        $rule->setCaster($caster);

        $this->assertSame('CASTED', $rule->apply('value'));
    }
}
