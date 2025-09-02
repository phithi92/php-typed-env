<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use DateException;
use DateTime;
use DateTimeInterface;
use Phithi92\TypedEnv\Contracts\ConstraintInterface;
use Phithi92\TypedEnv\Exception\ConstraintException;

final class MinDateConstraint implements ConstraintInterface
{
    private const DEFAULT_FORMAT = 'Y-m-d H:i:s';
    private DateTimeInterface $minDate;

    private string $format;

    public function __construct(string|DateTimeInterface $minDate, string $format = self::DEFAULT_FORMAT)
    {
        if (is_string($minDate)) {
            try {
                $minDate = new DateTime($minDate);
            } catch (DateException $e) {
                throw new ConstraintException('Invalid date/time syntax for ' . self::class, 0, $e);
            }
        }

        $this->minDate = $minDate;
        $this->format = $format;
    }

    public function assert(string $key, mixed $raw): mixed
    {
        if (! is_string($raw) && ! $raw instanceof DateTimeInterface) {
            throw new ConstraintException(
                sprintf(
                    'Expected string, got %s.',
                    is_object($raw) ? $raw::class : gettype($raw)
                )
            );
        }

        if (is_string($raw)) {
            try {
                $date = new DateTime($raw);
            } catch (DateException $ex) {
                throw new ConstraintException(
                    sprintf('Invalid date/time syntax for value: %s', $raw),
                    0,
                    $ex
                );
            }
        } else {
            $date = $raw;
        }

        if ($date->getTimestamp() <= $this->minDate->getTimestamp()) {
            throw new ConstraintException(
                sprintf(
                    'Date must be after or equal to %s. Got %s',
                    $this->minDate->format($this->format),
                    $date->getTimestamp()
                )
            );
        }

        return $raw;
    }
}
