<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use Phithi92\TypedEnv\Contracts\CasterInterface;
use Phithi92\TypedEnv\Exception\CastException;

final class UuidAnyCaster implements CasterInterface
{
    private const UUID_REGEX = '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';
    private const UUIDV4_REGEX = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';

    private bool $uuidV4;

    public function __construct(bool $uuidV4)
    {
        $this->uuidV4 = $uuidV4;
    }

    public function cast(string $key, string $raw): string
    {
        $regex = $this->uuidV4 ? self::UUIDV4_REGEX : self::UUID_REGEX;
        $type = $this->uuidV4 ? 'UUIDV4' : 'UUID';

        if (preg_match($regex, $raw) !== 1) {
            throw new CastException("ENV {$key}: '{$raw}' is not a valid {$type}");
        }

        return $raw;
    }
}
