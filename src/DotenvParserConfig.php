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
     *   If false, the "export" prefix is treated as part of the key,
     *   unless strict mode is enabled, in which case an exception is thrown.
     *
     * @param bool $allowValueComments
     *   If true, unquoted comment delimiters ("#" or ";") inside a value mark the start
     *   of an inline comment and are removed.
     *   Example: "FOO=bar # comment" => "bar".
     *   Quoted values never have inline comments stripped.
     *   If false, everything after "=" is treated as part of the value.
     *
     * @param bool $allowSectionComments
     *   If true, section headers may have trailing comments after the closing bracket.
     *   Example: "[app.db] # reconnect".
     *   If false, such comments are not allowed (strict mode will throw).
     *
     * @param bool $allowFullLineComments
     *   If true, lines starting with "#" or ";" are treated as comments and skipped.
     *   If false, such lines are considered invalid (strict mode will throw).
     *
     * @param bool $strictMode
     *   If true, parsing errors (e.g. missing "=", empty keys, unclosed quotes,
     *   duplicate keys, scalar/section conflicts, disallowed comments) will throw
     *   DotenvSyntaxException.
     *   If false, the parser is more lenient and may skip or reinterpret invalid lines.
     */
    public function __construct(
        public bool $allowExport = false,
        public bool $allowValueComments = true,     // inline comments in KEY=VALUE (unquoted)
        public bool $allowSectionComments = true,   // trailing comments after section headers
        public bool $allowFullLineComments = true,  // whole-line comments (#... or ;...)
        public bool $strictMode = true,
    ) {
    }
}
