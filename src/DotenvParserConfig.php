<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv;

/**
 * Configuration options for the DotenvParser.
 *
 * These flags control how .env files are interpreted.
 */
final class DotenvParserConfig
{
    /**
     * @param bool $allowExport
     *   If true, lines starting with "export " are allowed
     *   (e.g. "export FOO=bar" will be parsed as "FOO=bar").
     *   If false, the "export" prefix is treated as part of the key.
     *
     * @param bool $allowInlineComments
     *   If true, unquoted "#" characters inside a value mark the start
     *   of an inline comment and are removed.
     *   Example: "FOO=bar # comment" => "bar".
     *   If false, everything after "=" is treated as part of the value.
     *
     * @param bool $strictMode
     *   If true, parsing errors (e.g. missing "=" or empty key) throw exceptions.
     *   If false, the parser may choose to skip invalid lines silently.
     *   (Currently not fully implemented, reserved for future use.)
     */
    public function __construct(
        public bool $allowExport = false,
        public bool $allowInlineComments = false,
        public bool $strictMode = true,
    ) {
    }
}
