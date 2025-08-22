<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv;

use Phithi92\TypedEnv\Exception\DotenvFileException;
use Phithi92\TypedEnv\Exception\MissingEnvVariableException;

final class EnvKit
{
    /** @var array<string, mixed> */
    private array $values = [];

    private readonly DotenvParser $parser;

    public function __construct(?DotenvParser $parser = null)
    {
        $this->parser = $parser ?? new DotenvParser();
    }

    public static function load(string $path): self
    {
        return (new EnvKit())->loadDotenv($path);
    }

    public function loadDotenv(string $path): self
    {
        if (! file_exists($path)) {
            throw new DotenvFileException('No file found');
        }

        /** @var array<string,string> $parsed */
        $parsed = $this->parser->parse($path);
        // Here: dotenv values override existing raw values
        $this->values = array_replace($this->values, $parsed);

        return $this;
    }

    public function validate(Schema $schema): self
    {
        /** @var array<string, mixed> $validated */
        $validated = [];

        foreach ($schema->all() as $key => $rule) {
            $hasValue = array_key_exists($key, $this->values) || array_key_exists($key, $_ENV);

            if (! $hasValue) {
                if ($rule->hasDefault()) {
                    // Default may already be typed — apply() accepts mixed
                    $validated[$key] = $rule->apply($rule->getDefault());
                    continue;
                }
                if ($rule->isRequired()) {
                    throw new MissingEnvVariableException("Missing environment variable: {$key}");
                }
                // Optional without default → skip
                continue;
            }

            // Prefer loaded values, fallback to $_ENV
            // Can be string (raw) or already typed (from previous validate())
            $raw = $this->values[$key] ?? $_ENV[$key];

            // apply() only casts if $raw is a string; otherwise just runs constraints
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
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->values;
    }
}
