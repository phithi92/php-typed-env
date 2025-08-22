<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use DateTimeImmutable;
use DateTimeInterface;
use Phithi92\TypedEnv\Contracts\CasterInterface;
use Phithi92\TypedEnv\Exception\CastException;

final class DateCaster implements CasterInterface
{
    public function __construct(private string $format = 'Y-m-d')
    {
    }

    public function cast(string $key, string $raw): DateTimeInterface
    {
        $dt = DateTimeImmutable::createFromFormat($this->format, $raw);
        if ($dt === false) {
            throw new CastException("ENV {$key}: '{$raw}' does not match date format '{$this->format}'");
        }
        $err = DateTimeImmutable::getLastErrors();
        if ($err !== false && ($err['error_count'] > 0 || ($err['warning_count']) > 0)) {
            throw new CastException("ENV {$key}: '{$raw}' is not a valid date ({$this->format})");
        }
        return $dt;
    }
}
