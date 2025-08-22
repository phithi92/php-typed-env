<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use Phithi92\TypedEnv\Contracts\ConstraintInterface;
use Phithi92\TypedEnv\Exception\ConstraintException;

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
        // only work with scalar values
        if (! is_scalar($value)) {
            throw new ConstraintException(sprintf(
                'ENV %s: unsupported non-scalar value %s for EnumConstraint',
                $key,
                get_debug_type($value),
            ));
        }

        if (! in_array($value, $this->allowed, true)) {
            $list = implode(', ', array_map('strval', $this->allowed));

            throw new ConstraintException(sprintf(
                'ENV %s: %s not in [%s]',
                $key,
                var_export($value, true),
                $list
            ));
        }

        return $value;
    }
}
