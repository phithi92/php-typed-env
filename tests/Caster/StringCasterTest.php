<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Caster\StringCaster;
use Phithi92\TypedEnv\Exception\CastException;

#[CoversClass(StringCaster::class)]
final class StringCasterTest extends TestCase
{
    public function testReturnsStringUnchanged(): void
    {
        $caster = new StringCaster();
        $value  = 'hello world';

        $this->assertSame($value, $caster->cast('APP_NAME', $value));
    }

    public function testAcceptsEmptyString(): void
    {
        $caster = new StringCaster();

        $this->assertSame('', $caster->cast('EMPTY_KEY', ''));
    }

    public function testAcceptsNumericString(): void
    {
        $caster = new StringCaster();

        $this->assertSame('123', $caster->cast('NUM_KEY', '123'));
    }

    #[DataProvider('provideNonStringValues')]
    public function testThrowsOnNonStringValues(mixed $input, string $expectedType, string $key): void
    {
        $caster = new StringCaster();

        $this->expectException(CastException::class);
        // Message format: "ENV 'KEY': Expected a string value, got TYPE."
        $this->expectExceptionMessageMatches(
            sprintf(
                "/^ENV '%s': Expected a string value, got %s\\.$/",
                preg_quote($key, '/'),
                preg_quote($expectedType, '/')
            )
        );

        $caster->cast($key, $input);
    }

    public static function provideNonStringValues(): array
    {
        return [
            'integer'         => [42, 'integer', 'INT_KEY'],
            // Hinweis: gettype() nennt Floats "double"
            'float(double)'   => [3.14, 'double', 'FLOAT_KEY'],
            'boolean true'    => [true, 'boolean', 'BOOL_KEY'],
            'boolean false'   => [false, 'boolean', 'BOOL_KEY2'],
            'null'            => [null, 'NULL', 'NULL_KEY'],
            'array'           => [[1, 2, 3], 'array', 'ARRAY_KEY'],
            'object'          => [new \stdClass(), 'object', 'OBJECT_KEY'],
            // Closures sind Objekte
            'closure'         => [static fn () => null, 'object', 'CALLABLE_KEY'],
        ];
    }
}
