<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Caster\JsonCaster;
use Phithi92\TypedEnv\Exception\CastException;

final class JsonCasterTest extends TestCase
{
    private const VALID_CASES = [
        'simple object'   => ['{"a":1,"b":2}', ['a' => 1, 'b' => 2]],
        'empty object'    => ['{}', []],
        'array of ints'   => ['[1,2,3]', [1,2,3]],
        'nested object'   => ['{"user":{"id":1,"name":"Alice"}}', ['user' => ['id' => 1, 'name' => 'Alice']]],
        'boolean true'    => ['true', true],
        'boolean false'   => ['false', false],
        'null literal'    => ['null', null],
        'number literal'  => ['42', 42],
        'string literal'  => ['"hello"', 'hello'],
    ];

    private const INVALID_CASES = [
        'malformed json'  => '{bad json}',
        'trailing comma'  => '{"a":1,}',
        'unterminated'    => '{"a": 1',
        'wrong quotes'    => "{'a':1}",
        'empty string'    => '',
        'whitespace only' => '   ',
    ];

    public function testValidJson(): void
    {
        $caster = new JsonCaster(true);

        foreach (self::VALID_CASES as $label => [$input, $expected]) {
            $out = $caster->cast('J', $input);
            $this->assertEquals(
                $expected,
                $out,
                "Failed asserting valid JSON case: {$label}"
            );
        }
    }

    public function testInvalidJson(): void
    {
        $caster = new JsonCaster(true);

        foreach (self::INVALID_CASES as $label => $input) {
            try {
                $caster->cast('J', $input);
                $this->fail("Expected CastException for case: {$label}");
            } catch (CastException $e) {
                $this->assertTrue(true); // expected
            }
        }
    }
}
