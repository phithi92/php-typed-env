<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use InvalidArgumentException;
use Phithi92\TypedEnv\Contracts\ConstraintInterface;

final class EnumConstraint implements ConstraintInterface
{
    /** @var list<scalar> */
    private array $allowed;

    /** @param list<scalar> $allowed */
    public function __construct(array $allowed)
    {
        $this->allowed = $allowed;
    }

    /** @param mixed $value @return scalar */
    public function assert(string $key, mixed $value): mixed
    {
        // Harte Eingrenzung: Enum arbeitet nur mit skalaren Werten
        if (! is_scalar($value)) {
            throw new InvalidArgumentException(sprintf(
                'ENV %s: unsupported non-scalar value %s for EnumConstraint',
                $key,
                get_debug_type($value),
            ));
        }

        if (! in_array($value, $this->allowed, true)) {
            // keine (string)-Casts auf mixed: strval() + vorher list<scalar>
            $list = implode(', ', array_map('strval', $this->allowed));

            // keine Interpolation von $value (mixed): var_export → string
            throw new InvalidArgumentException(sprintf(
                'ENV %s: %s not in [%s]',
                $key,
                var_export($value, true),
                $list
            ));
        }

        return $value;
    }
}
