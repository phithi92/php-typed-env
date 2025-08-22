<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use Phithi92\TypedEnv\Exception\CastException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\KeyRule;

final class BoolCasterTest extends TestCase
{
    #[DataProvider('provideTrueTokens')]
    public function testTrueTokens(string $raw): void
    {
        $rule = (new KeyRule('FLAG'))->typeBool();
        self::assertTrue($rule->apply($raw));
    }

    #[DataProvider('provideFalseTokens')]
    public function testFalseTokens(string $raw): void
    {
        $rule = (new KeyRule('FLAG'))->typeBool();
        self::assertFalse($rule->apply($raw));
    }

    public function testInvalidBool(): void
    {
        $rule = (new KeyRule('FLAG'))->typeBool();
        $this->expectException(CastException::class);
        $rule->apply('maybe');
    }

    public static function provideTrueTokens(): iterable
    {
        yield ['1'];
        yield ['true'];
        yield ['on'];
        yield ['yes'];
        yield ['TRUE'];
        yield ['On'];
    }

    public static function provideFalseTokens(): iterable
    {
        yield ['0'];
        yield ['false'];
        yield ['off'];
        yield ['no'];
        yield ['FALSE'];
        yield ['Off'];
    }
}
