<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use Phithi92\TypedEnv\Exception\CastException;
use Phithi92\TypedEnv\Caster\IntCaster;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class IntCasterTest extends TestCase
{
    #[DataProvider('okCases')]
    public function testCastOk(string $raw, int $expected): void
    {
        $caster = new IntCaster();
        self::assertSame($expected, $caster->cast('N', $raw));
    }

    #[DataProvider('badCases')]
    public function testCastBad(string $raw): void
    {
        $caster = new IntCaster();

        $this->expectException(CastException::class);
        $this->expectExceptionMessageMatches("/^ENV N: '.*' is not a valid int$/");

        $caster->cast('N', $raw);
    }

    public static function okCases(): array
    {
        return [
            'zero'            => ['0', 0],
            'positive'        => ['42', 42],
            'negative'        => ['-7', -7],
            'leading zeros'   => ['007', 7],
            'trim whitespace' => [" \t -12 \n", -12],
        ];
    }

    public static function badCases(): array
    {
        return [
            'empty string' => [''],
            'spaces only'  => ['   '],
            'float'        => ['1.2'],
            'scientific'   => ['1e3'],
            'plus sign'    => ['+3'],
            'letters'      => ['abc'],
            'double minus' => ['--1'],
        ];
    }
}
