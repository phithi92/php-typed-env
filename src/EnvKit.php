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

    private function loadParsedValues(): void
    {
        /** @var array<string,string> $parsed */
        $parsed = $this->parser->parse();
        // Here: dotenv values override existing raw values
        $this->values = array_replace($this->values, $parsed);
    }
}
