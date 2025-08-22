<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\KeyRule;
use Phithi92\TypedEnv\Contracts\CasterInterface;
use Phithi92\TypedEnv\Contracts\ConstraintInterface;

final class KeyRuleTest extends TestCase
{
    private function spyCaster(mixed $returns, ?array &$calls = null): CasterInterface
    {
        return new class ($returns, $calls) implements CasterInterface {
            public function __construct(private mixed $ret, private ?array &$calls)
            {
            }
            public function cast(string $key, string $value): mixed
            {
                if (is_array($this->calls)) {
                    $this->calls[] = ['key' => $key, 'value' => $value];
                }
                return $this->ret;
            }
        };
    }

    private function appendConstraint(string $suffix, ?array &$calls = null): ConstraintInterface
    {
        return new class ($suffix, $calls) implements ConstraintInterface {
            public function __construct(private string $suffix, private ?array &$calls)
            {
            }
            public function assert(string $key, mixed $value): mixed
            {
                if (is_array($this->calls)) {
                    $this->calls[] = ['key' => $key, 'in' => $value];
                }
                return (string)$value . $this->suffix;
            }
        };
    }

    private function rule(string $key = 'T'): KeyRule
    {
        return new KeyRule($key);
    }

    // ---------- Tests --------------------------------------------------------

    public function testApplyUsesCasterForStringAndPassesKeyThenValue(): void
    {
        $calls = [];
        $caster = $this->spyCaster('CASTED', $calls);

        $r = $this->rule('T')->setCaster($caster);

        $out = $r->apply('raw');
        $this->assertSame('CASTED', $out);
        $this->assertSame([['key' => 'T', 'value' => 'raw']], $calls);
    }

    public function testApplySkipsCasterForNonStringAndRunsConstraintsInOrder(): void
    {
        $casterCalls = [];
        $constraintCalls = [];
        $caster = $this->spyCaster('SHOULD_NOT_BE_USED', $casterCalls);

        $r = $this->rule('T')
            ->setCaster($caster) // darf bei non-string NICHT aufgerufen werden
            ->addConstraint($this->appendConstraint('-A', $constraintCalls))
            ->addConstraint($this->appendConstraint('-B', $constraintCalls));

        $out = $r->apply(100); // non-string
        $this->assertSame('100-A-B', $out);
        $this->assertSame([], $casterCalls, 'Caster darf bei non-string nicht aufgerufen werden');
        $this->assertCount(2, $constraintCalls);
        $this->assertSame('T', $constraintCalls[0]['key']);
        $this->assertSame('T', $constraintCalls[1]['key']);
    }

    public function testFallbackToStringCasterWhenNoCasterIsSet(): void
    {
        // Kein Caster gesetzt → sollte den raw string unverändert durchlassen (StringCaster-Fallback)
        $out = $this->rule('X')->apply('abc');
        $this->assertSame('abc', $out);
    }

    public function testFlagsAndGetters(): void
    {
        $r = $this->rule('FOO');
        $this->assertTrue($r->isRequired());
        $this->assertFalse($r->hasDefault());

        $r->optional();
        $this->assertFalse($r->isRequired());

        $r->default(42);
        $this->assertFalse($r->isRequired());
        $this->assertTrue($r->hasDefault());
        $this->assertSame(42, $r->getDefault());
        $this->assertSame('FOO', $r->key());
    }
}
