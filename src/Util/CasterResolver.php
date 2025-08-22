<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Util;

use InvalidArgumentException;
use Phithi92\TypedEnv\Caster;
use Phithi92\TypedEnv\Contracts;

final class CasterResolver
{
    /**
     * @param array{
     *      assoc?: bool,
     *      delimiter?: string,
     *      allowEmpty?: bool,
     *      format?: string,
     *      immutable?: bool,
     *      realpath?: bool
     * } $options
     */
    public function resolve(string $type, array $options = []): Contracts\CasterInterface
    {
        try {
            return match ($type) {
                'string' => new Caster\StringCaster(),
                'bool' => new Caster\BoolCaster(),
                'int' => new Caster\IntCaster(),
                'float' => new Caster\FloatCaster(),
                'duration' => new Caster\DurationCaster(),
                'json' => new Caster\JsonCaster($options['assoc'] ?? true),
                'list' => new Caster\ListCaster($options['delimiter'] ?? ',', $options['allowEmpty'] ?? false),
                'url' => new Caster\UrlCaster(),
                'email' => new Caster\EmailCaster(),
                'ip' => new Caster\IpCaster(),
                'uuid' => new Caster\UuidAnyCaster(),
                'uuid4' => new Caster\UuidV4Caster(),
                'size' => new Caster\SizeCaster(),
                'port' => new Caster\PortCaster(),
                'datetime' => new Caster\DateTimeCaster($options['format'] ?? 'c', $options['immutable'] ?? true),
                'path' => new Caster\PathCaster($options['realpath'] ?? false),
                default => throw new InvalidArgumentException("Unknown caster type: {$type}"),
            };
        } catch (\TypeError $e) {
            throw new InvalidArgumentException("Invalid options for caster '{$type}': {$e->getMessage()}", 0, $e);
        }
    }
}
