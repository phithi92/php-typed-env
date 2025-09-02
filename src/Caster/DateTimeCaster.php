<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use DateTime;
use DateTimeImmutable;
use Phithi92\TypedEnv\Contracts\CasterInterface;
use Phithi92\TypedEnv\Exception\CastException;

final class DateTimeCaster implements CasterInterface
{
    public function __construct(
        private string $format,
        private bool $immutable
    ) {
    }

    public function cast(string $key, string $raw): DateTime|DateTimeImmutable
    {
        $class = $this->resolveDateTimeClass();

        $dt = $class::createFromFormat($this->format, $raw);
        if ($dt === false) {
            throw new CastException("ENV {$key}: '{$raw}' does not match datetime format '{$this->format}'");
        }

        $err = $class::getLastErrors();
        if ($err !== false && (($err['error_count'] ?? 0) > 0 || ($err['warning_count'] ?? 0) > 0)) {
            throw new CastException("ENV {$key}: '{$raw}' is not a valid date/time ({$this->format})");
        }

        return $dt;
    }

    public function resolveDateTimeClass(): DateTime|DateTimeImmutable
    {
        return new ($this->immutable ? DateTimeImmutable::class : DateTime::class);
    }
}
