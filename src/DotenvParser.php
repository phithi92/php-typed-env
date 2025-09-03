<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv;

use Phithi92\TypedEnv\Contracts\HandlerInterface;
use Phithi92\TypedEnv\Exception\DotenvFileException;
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
     * @return array<string,string>
     *
     * @throws DotenvSyntaxException|DotenvFileException
     */
    public function parse(): array
    {
        $result = [];

        while ($this->handler->hasMore()) {
            $line = $this->handler->read() ?? '';
            $lineNo = $this->handler->current();

            $processedLine = $this->normalizeLine($line, $lineNo);
            if ($processedLine === false) {
                continue;
            }

            [$key, $value] = $this->splitKeyValue($processedLine, $lineNo);

            $result[$key] = $this->normalizeValue($value);
        }

        return $result;
    }

    // ---------- Helpers ---------------------------------------------------

    private function normalizeLine(string $line, int $lineNo): string|false
    {
        $line = rtrim($line, "\r\n");

        // strip UTF-8 BOM on first line if present
        if ($lineNo === 1 && str_starts_with($line, "\xEF\xBB\xBF")) {
            $line = substr($line, 3);
        }

        if ($this->isSkippable($line)) {
            return false;
        }

        // support optional "export " prefix
        if ($this->config->allowExport) {
            $ltrim = ltrim($line);
            if (str_starts_with($ltrim, 'export ')) {
                // remove only the first occurrence at line start
                $line = preg_replace('/^\s*export\s+/', '', $line, 1);
                if ($line === null) {
                    throw new DotenvSyntaxException(
                        sprintf(
                            "Regex error while trying to strip 'export' prefix from line: '%s'",
                            $ltrim
                        )
                    );
                }
            }
        }

        return $line;
    }

    private function isSkippable(string $line): bool
    {
        $trimmed = trim($line);
        return $trimmed === '' || $trimmed[0] === '#';
    }

    /**
     * @return array{0:string,1:string}
     */
    private function splitKeyValue(string $line, int $lineNo): array
    {
        $parts = explode('=', $line, 2);
        if (count($parts) < 2) {
            throw new DotenvSyntaxException("Invalid .env line {$lineNo}: missing '='");
        }

        $key = trim($parts[0]);
        $val = trim($parts[1]);

        if ($key === '') {
            throw new DotenvSyntaxException("Invalid .env line {$lineNo}: empty key");
        }

        return [$key, $val];
    }

    private function normalizeValue(string $val): string
    {
        if ($this->isSurroundedBySameQuote($val)) {
            return substr($val, 1, -1);
        }

        if ($this->config->allowInlineComments) {
            $hashPos = strpos($val, '#');
            if ($hashPos !== false) {
                $val = rtrim(substr($val, 0, $hashPos));
            }
        }

        return $val;
    }

    private function isSurroundedBySameQuote(string $value): bool
    {
        $len = strlen($value);
        if ($len < 2) {
            return false;
        }
        $first = $value[0];
        $last = $value[$len - 1];
        return ($first === '"' && $last === '"') || ($first === "'" && $last === "'");
    }
}
