<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv;

use InvalidArgumentException;
use RuntimeException;
use SplFileObject;

final class DotenvParser
{
    private readonly DotenvParserConfig $config;

    public function __construct(?DotenvParserConfig $config = null)
    {
        $this->config = $config ?? new DotenvParserConfig();
    }

    /**
     * @return array<string,string>
     *
     * @throws RuntimeException|InvalidArgumentException
     */
    public function parse(string $path): array
    {
        $result = [];
        $lineNo = 0;

        $h = $this->openHandle($path);
        while (! $h->eof()) {
            $line = $h->fgets();
            $lineNo++;
            $trimmedLine = rtrim($line, "\r\n");

            $processedLine = $this->preprocessLine($trimmedLine, $lineNo);
            if ($this->isSkippable($processedLine)) {
                continue;
            }

            [$key, $val] = $this->splitKeyValue($processedLine, $lineNo);

            $result[$key] = $this->normalizeValue($val);

            $h->next();
        }

        return $result;
    }

    // ---------- Helpers ---------------------------------------------------

    private function openHandle(string $path): SplFileObject
    {
        if (! is_file($path)) {
            throw new RuntimeException("Dotenv file not found: {$path}");
        }

        try {
            return new SplFileObject($path, 'rb');
        } catch (RuntimeException $e) {
            throw new RuntimeException("Unable to open dotenv file: {$path}", 0, $e);
        }
    }

    private function preprocessLine(string $line, int $lineNo): string
    {
        // strip UTF-8 BOM on first line if present
        if ($lineNo === 1 && str_starts_with($line, "\xEF\xBB\xBF")) {
            $line = substr($line, 3);
        }

        // support optional "export " prefix
        if ($this->config->allowExport) {
            $ltrim = ltrim($line);
            if (str_starts_with($ltrim, 'export ')) {
                // remove only the first occurrence at line start
                $line = preg_replace('/^\s*export\s+/', '', $line, 1);
            }
        }

        return $line ?? '';
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
            throw new InvalidArgumentException("Invalid .env line {$lineNo}: missing '='");
        }

        $key = trim($parts[0]);
        $val = trim($parts[1]);

        if ($key === '') {
            throw new InvalidArgumentException("Invalid .env line {$lineNo}: empty key");
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
