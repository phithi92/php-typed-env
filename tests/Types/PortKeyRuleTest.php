<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Types;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Types\PortKeyRule;
use Phithi92\TypedEnv\Exception\CastException;
use Phithi92\TypedEnv\Exception\ConstraintException;

final class PortKeyRuleTest extends TestCase
{
    public function testValidPortWithinRange(): void
    {
        $rule = new PortKeyRule('PORT');
        $rule->range(1024, 65535);

        $this->assertSame(8080, $rule->apply('8080'));
    }

    public function testInvalidPortCastHasMessage(): void
    {
        $this->expectException(CastException::class);
        $this->expectExceptionMessage("ENV PORT: 'eighty' is not a valid port");

        (new PortKeyRule('PORT'))->apply('eighty');
    }

    public function testSettingInvalidMinThrowsConstraintException(): void
    {
        $rule = new PortKeyRule('PORT');

        $this->expectException(ConstraintException::class);
        $this->expectExceptionMessage('Minimum port value must be between 0 and 65535.');
        $rule->min(-1);
    }

    public function testPortBelowMinViolatesConstraintMessage(): void
    {
        $rule = new PortKeyRule('PORT');
        $rule->min(1024);

        $this->expectException(ConstraintException::class);
        $this->expectExceptionMessage('ENV PORT: value 22 < min 1024');
        $rule->apply('22');
    }
}
