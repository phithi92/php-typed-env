<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use Phithi92\TypedEnv\Contracts\CasterInterface;

final class DateTimeCaster implements CasterInterface
{
    public function __construct(private string $format = 'c', private bool $immutable = true)
    {
    }
    public function cast(string $key, string $raw): DateTimeInterface
    {
        $dt = $this->immutable
            ? DateTimeImmutable::createFromFormat($this->format, $raw)
            : DateTime::createFromFormat($this->format, $raw);

        if ($dt === false) {
            throw new InvalidArgumentException("ENV {$key}: '{$raw}' does not match datetime format '{$this->format}'");
        }
        $err = DateTime::getLastErrors();
        if ($err !== false && ($err['error_count'] > 0 || ($err['warning_count']) > 0)) {
            throw new InvalidArgumentException("ENV {$key}: '{$raw}' is not a valid date/time ({$this->format})");
        }
        return $dt;
    }
}
