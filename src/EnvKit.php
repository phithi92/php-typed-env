<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv;

use Phithi92\TypedEnv\Exception\MissingEnvVariableException;
use Phithi92\TypedEnv\Schema\Schema;

final class EnvKit
{
    /** @var array<string, mixed> */
    private array $values = [];

    private readonly DotenvParser $parser;

    public function __construct(DotenvParser $parser)
    {
        $this->parser = $parser;
    }

    public static function load(DotenvParser $parser): self
    {
        return new self($parser);
    }

    public function validate(Schema $schema): self
    {
        // ensure values are loaded from the parser once
        if ($this->values === []) {
            $this->loadParsedValues();
        }

        /** @var array<string, mixed> $validated */
        $validated = [];

        $env = $_ENV;

        foreach ($schema as $key => $rule) {
            $raw = $this->values[$key] ?? $env[$key] ?? null;

            // No value provided
            if ($raw === null) {
                if ($rule->hasDefault()) {
                    // Use default (may already be typed)
                    $validated[$key] = $rule->apply($rule->getDefault());
                    continue;
                }

                if ($rule->isRequired()) {
                    throw new MissingEnvVariableException("Missing environment variable: {$key}");
                }

                // Optional without default then skip
                continue;
            }

            // Value found then validate and cast if needed
            $validated[$key] = $rule->apply($raw);
        }

        $this->values = $validated;
        return $this;
    }

    public function get(string $key): mixed
    {
        return $this->values[$key] ?? null;
    }

    /**
     * Flattens a nested array (e.g. sections) into dot-notated keys.
     *
     * Wichtig: Parser liefert nur Arrays (für Sektionen) oder Strings (für Blätter).
     * Wir erzwingen das hier zur Sicherheit und zugunsten von Static Analysis.
     *
     * @param array<mixed,mixed> $data   Arbitrary nested array (keys may be mixed, values mixed)
     *
     * @return array<string,string>      Dot-notated keys, string leaves
     */
    private function flatten(array $data, string $prefix = ''): array
    {
        /** @var array<string,string> $out */
        $out = [];

        foreach ($data as $key => $val) {
            // Normalisiere Key auf string (Parser erzeugt string-Keys; dies ist defensiv)
            $keyStr = is_string($key) ? $key : (string) $key;
            $newKey = $prefix === '' ? $keyStr : "{$prefix}.{$keyStr}";

            if (is_array($val)) {
                // recurse into sections
                $out += $this->flatten($val, $newKey);
                continue;
            }

            // Enforce: leaves must be string (Parser liefert strings; Assertion hilft PHPStan)
            if (! is_string($val)) {
                throw new \LogicException('EnvKit::flatten expects leaf values to be string.');
            }

            $out[$newKey] = $val;
        }

        return $out;
    }

    private function loadParsedValues(): void
    {
        /** @var array<mixed,mixed> $parsed */
        $parsed = $this->parser->parse();

        /** @var array<string,string> $flattened */
        $flattened = $this->flatten($parsed);

        // Here: dotenv values override existing raw values
        $this->values = array_replace($this->values, $flattened);
    }
}
