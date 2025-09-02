<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use Phithi92\TypedEnv\Contracts\ConstraintInterface;
use Phithi92\TypedEnv\Exception\ConstraintException;

final class EnumListConstraint implements ConstraintInterface
{
    /** @var list<scalar> */
    private array $allowed;

    /** @param list<scalar> $allowed */
    public function __construct(array $allowed)
    {
        $this->allowed = $allowed;
    }

    /**
     * @return array<mixed>
     *
     * @throws ConstraintException
     */
    public function assert(string $key, mixed $values): array
    {
        if (! is_array($values)) {
            throw new ConstraintException("ENV {$key}: no valid array.");
        }

        $c = new EnumConstraint($this->allowed);

        foreach ($values as $idx => $val) {
            $c->assert($key, $val);
        }

        return $values;
    }
}
