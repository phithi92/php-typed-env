<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv;

use Phithi92\TypedEnv\Contracts\HandlerInterface;
use Phithi92\TypedEnv\Exception\DotenvSyntaxException;

final class DotenvParser
{
    private readonly DotenvParserConfig $config;
    private readonly HandlerInterface $handler;

    public function __construct(HandlerInterface $handler, ?DotenvParserConfig $config = null)
    {
        $this->config = $config ?? new DotenvParserConfig();
        $this->handler = $handler;
    }

    /**
     * Parse handler stream into an array.
     *
     * @return array<string,mixed>
     *
     * @throws DotenvSyntaxException
     */
    public function parse(): array
    {
        /** @var array<string,mixed> $result */
        $result = [];
        /** @var list<string> $currentPath */
        $currentPath = [];
        /** @var array<string,mixed> $sectionRef */
        $sectionRef = &$result;
        $handler = $this->handler;

        $first = true;

        while ($handler->hasMore()) {
            $line = $handler->read() ?? '';
            $lineNo = $handler->current();

            // Strip BOM only on the very first physical line
            if ($first) {
                $first = false;
                if ($line !== '' && str_starts_with($line, "\xEF\xBB\xBF")) {
                    $line = substr($line, 3);
                }
            }

            // Normalize line & skip empty/comment
            $line = $this->normalizePhysicalLine($line, $lineNo);
            if ($line === false) {
                continue;
            }

            // Section line?
            $section = $this->extractSectionPathIfAny($line, $lineNo);
            if ($section !== null) {
                $currentPath = $this->splitSectionPath($section, $lineNo);
                $sectionRef = &$this->resolveSectionRef($result, $currentPath, $lineNo);
                continue;
            }

            // Key / Value
            [$key, $rawVal] = $this->splitKeyValueFast($line, $lineNo);
            $key = $this->applyExportRules($key, $lineNo);

            if ($key === '') {
                throw new DotenvSyntaxException("Empty key at line {$lineNo}");
            }

            // strict: duplicate key in same section
            if ($this->config->strictMode && array_key_exists($key, $sectionRef)) {
                throw new DotenvSyntaxException("Duplicate key '{$key}' at line {$lineNo}");
            }

            $value = $this->parseValue($rawVal, $lineNo);
            $sectionRef[$key] = $value;
        }

        return $result;
    }

    /**
     * Trim right, handle full-line comments according to config.
     *
     * @throws DotenvSyntaxException
     */
    private function normalizePhysicalLine(string $line, int $lineNo): string|false
    {
        $line = rtrim($line, " \t\r\n");

        if ($line === '') {
            return false;
        }

        // full-line comment? (# or ;)
        $lt = ltrim($line);
        $first = $lt[0] ?? '';
        if ($first === '#' || $first === ';') {
            if (! $this->config->allowFullLineComments && $this->config->strictMode) {
                throw new DotenvSyntaxException("Full-line comments are not allowed at line {$lineNo}");
            }
            return false;
        }

        return $line;
    }

    /**
     * Fast section detection & validation.
     * Returns section path (string inside [ ... ]) or null if not a section line.
     *
     * @throws DotenvSyntaxException
     */
    private function extractSectionPathIfAny(string $line, int $lineNo): ?string
    {
        // skip leading spaces
        $i = 0;
        $len = strlen($line);
        while ($i < $len && $line[$i] === ' ') {
            $i++;
        }

        if ($i >= $len || $line[$i] !== '[') {
            return null; // not a section
        }

        // find closing bracket
        $j = strpos($line, ']', $i + 1);
        if ($j === false) {
            if ($this->config->strictMode) {
                throw new DotenvSyntaxException("Unclosed section header at line {$lineNo}");
            }
            // lenient: treat as non-section
            return null;
        }

        $inside = trim(substr($line, $i + 1, $j - $i - 1));

        // trailing content after ']'
        $trail = ltrim(substr($line, $j + 1));
        if ($trail !== '') {
            $c = $trail[0];
            $isComment = ($c === '#' || $c === ';');

            if ($isComment) {
                if (! $this->config->allowSectionComments && $this->config->strictMode) {
                    throw new DotenvSyntaxException("Section trailing comments are not allowed at line {$lineNo}");
                }
                // allowed or lenient: ignore trailing comment
            } else {
                // Non-comment trailing text after ']' is invalid in strict mode
                if ($this->config->strictMode) {
                    throw new DotenvSyntaxException("Trailing characters after section header at line {$lineNo}");
                }
                // lenient: ignore it
            }
        }

        return $inside;
    }

    /**
     * Split dotted section path into segments; strict: no empty segments.
     *
     * @return list<string>
     *
     * @throws DotenvSyntaxException
     */
    private function splitSectionPath(string $section, int $lineNo): array
    {
        if (strpos($section, '.') === false) {
            if ($section === '' && $this->config->strictMode) {
                throw new DotenvSyntaxException("Empty section name at line {$lineNo}");
            }
            return $section === '' ? [] : [$section];
        }

        $parts = explode('.', $section);
        if ($this->config->strictMode) {
            foreach ($parts as $seg) {
                if ($seg === '') {
                    throw new DotenvSyntaxException("Empty section path segment at line {$lineNo}");
                }
            }
        }
        /** @var list<non-empty-string> */
        $filtered = array_values(array_filter($parts, static fn ($s) => $s !== ''));
        return $filtered;
    }

    /**
     * Faster split than regex: split on first '=' and trim.
     *
     * @return array{0:string,1:string}
     *
     * @throws DotenvSyntaxException
     */
    private function splitKeyValueFast(string $line, int $lineNo): array
    {
        $pos = strpos($line, '=');
        if ($pos === false) {
            throw new DotenvSyntaxException("Invalid key/value at line {$lineNo}: {$line}");
        }

        // IMPORTANT: trim BOTH sides of the key to normalize "   export   FOO"
        $left = trim(substr($line, 0, $pos));
        // Value: keep left-trim only; trailing spaces are meaningful unless cut by comment rule
        $right = ltrim(substr($line, $pos + 1));

        if ($left === '') {
            throw new DotenvSyntaxException("Empty key at line {$lineNo}");
        }

        return [$left, $right];
    }

    /**
     * Apply "export" semantics (lenient vs strict), honoring leading spaces already trimmed.
     *
     * @throws DotenvSyntaxException
     */
    private function applyExportRules(string $rawKey, int $lineNo): string
    {
        $key = trim($rawKey); // normalize

        // Check if key starts with "export" (case-insensitive)
        if (preg_match('/^export\b/i', $key) !== 1) {
            return $key;
        }

        if ($this->config->allowExport) {
            // remove "export" + spaces: "export   KEY" => "KEY"
            if (preg_match('/^export\s+(.*)\z/i', $key, $m) === 1) {
                $normalized = trim($m[1]);
                // If nothing remains after "export", strict mode should complain
                if ($normalized === '' && $this->config->strictMode) {
                    throw new DotenvSyntaxException("Missing key after `export` at line {$lineNo}");
                }
                return $normalized;
            }
            // "exportKEY" (no space) is not a formal export; keep literal
            return $key;
        }

        // export not allowed
        if ($this->config->strictMode) {
            throw new DotenvSyntaxException("`export` prefix is not allowed at line {$lineNo}");
        }

        // lenient: keep literal "export FOO"
        return $key;
    }

    /**
     * Parse value (quoted/unquoted + inline comments and strict checks).
     *
     * @throws DotenvSyntaxException
     */
    private function parseValue(string $raw, int $lineNo): string
    {
        $raw = ltrim($raw);
        if ($raw === '') {
            return '';
        }

        $ch = $raw[0];

        if ($ch === '"') {
            /** @var array{0:string,1:int,2:bool} $scan */
            $scan = $this->scanDoubleQuoted($raw);
            [$content, $endIdx, $closed] = $scan;
            if (! $closed && $this->config->strictMode) {
                throw new DotenvSyntaxException("Unclosed double-quoted value at line {$lineNo}");
            }
            $after = ltrim(substr($raw, $endIdx + 1));
            if ($after !== '') {
                // trailing allowed only if it's a comment and value comments are enabled
                if (! ($this->config->allowValueComments && ($after[0] === '#' || $after[0] === ';'))) {
                    if ($this->config->strictMode) {
                        throw new DotenvSyntaxException("Trailing characters after quoted value at line {$lineNo}");
                    }
                }
            }
            return stripcslashes($content);
        }

        if ($ch === "'") {
            /** @var array{0:string,1:int,2:bool} $scan */
            $scan = $this->scanSingleQuoted($raw);
            [$content, $endIdx, $closed] = $scan;
            if (! $closed && $this->config->strictMode) {
                throw new DotenvSyntaxException("Unclosed single-quoted value at line {$lineNo}");
            }
            $after = ltrim(substr($raw, $endIdx + 1));
            if ($after !== '') {
                if (! ($this->config->allowValueComments && ($after[0] === '#' || $after[0] === ';'))) {
                    if ($this->config->strictMode) {
                        throw new DotenvSyntaxException("Trailing characters after quoted value at line {$lineNo}");
                    }
                }
            }
            return $content;
        }

        // unquoted
        if ($this->config->allowValueComments) {
            $cut = strcspn($raw, '#;');
            if ($cut < strlen($raw)) {
                return rtrim(substr($raw, 0, $cut));
            }
        }
        return rtrim($raw);
    }

    /**
     * Read content inside starting double quote (supports escapes).
     *
     * @return array{0:string,1:int,2:bool} [content, endIndex, closed]
     */
    private function scanDoubleQuoted(string $s): array
    {
        $len = strlen($s);
        $i = 1; // after opening "
        $esc = false;
        for (; $i < $len; $i++) {
            $c = $s[$i];
            if ($c === '"' && ! $esc) {
                // content between 1 and i-1
                return [substr($s, 1, $i - 1), $i, true];
            }
            if ($c === '\\' && ! $esc) {
                $esc = true;
            } else {
                $esc = false;
            }
        }
        // no closing quote
        return [substr($s, 1), $len - 1, false];
    }

    /**
     * Read content inside starting single quote (no escapes).
     *
     * @return array{0:string,1:int,2:bool} [content, endIndex, closed]
     */
    private function scanSingleQuoted(string $s): array
    {
        $pos = strpos($s, "'", 1);
        if ($pos === false) {
            return [substr($s, 1), strlen($s) - 1, false];
        }
        return [substr($s, 1, $pos - 1), $pos, true];
    }

    /**
     * Traverse (and create if necessary) the section node and return a reference to it.
     *
     * @param array<string,mixed> $root
     * @param list<string>        $path
     *
     * @return array<string,mixed>
     *
     * @throws DotenvSyntaxException
     */
    private function &resolveSectionRef(array &$root, array $path, int $lineNo): array
    {
        $ref = &$root;

        foreach ($path as $seg) {
            if (isset($ref[$seg]) && ! is_array($ref[$seg])) {
                if ($this->config->strictMode) {
                    throw new DotenvSyntaxException("Cannot convert scalar '{$seg}' to section at line {$lineNo}");
                }
                $ref[$seg] = [];
            }
            if (! isset($ref[$seg]) || ! is_array($ref[$seg])) {
                $ref[$seg] = [];
            }
            /** @var array<string,mixed> $next */
            $next = &$ref[$seg];
            $ref = &$next;
        }

        return $ref;
    }
}
