<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use DateException;
use DateTime;
use DateTimeInterface;
use Phithi92\TypedEnv\Contracts\ConstraintInterface;
use Phithi92\TypedEnv\Exception\ConstraintException;

final class MaxDateConstraint implements ConstraintInterface
{
    private const DEFAULT_FORMAT = 'Y-m-d H:i:s';
    private DateTimeInterface $maxDate;

    private string $format;

    public function __construct(string|DateTimeInterface $maxDate, string $format = self::DEFAULT_FORMAT)
    {
        if (is_string($maxDate)) {
            try {
                $maxDate = new DateTime($maxDate);
            } catch (DateException $e) {
                throw new ConstraintException('Invalid date/time syntax for ' . self::class);
            }
        }
        $this->maxDate = $maxDate;
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

        if ($date->getTimestamp() >= $this->maxDate->getTimestamp()) {
            throw new ConstraintException(
                sprintf(
                    'Date must be before or equal to %s. Got %s',
                    $this->maxDate->format($this->format),
                    $date->getTimestamp()
                )
            );
        }

        return $raw;
    }
}
