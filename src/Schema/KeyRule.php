<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Schema;

use Phithi92\TypedEnv\Caster;
use Phithi92\TypedEnv\Contracts\CasterInterface;
use Phithi92\TypedEnv\Contracts\ConstraintInterface;
use Phithi92\TypedEnv\Exception\ConstraintException;

/**
 * KeyRule with fluent configuration and runtime apply().
 * - Constructor accepts only the key (and optional caster).
 * - Casts only when input is a string; otherwise runs constraints.
 */
abstract class KeyRule
{
    /** Optional caster used to convert raw values to the expected type */
    private ?CasterInterface $caster = null;

    /** @var list<ConstraintInterface> Validation constraints applied to the value */
    private array $constraints = [];

    /** Whether the environment variable is required */
    private bool $required = true;

    /** Whether a default value has been explicitly set */
    private bool $hasDefault = false;

    /** Holds the default value (typed or untyped) */
    private mixed $default = null;

    /** Name of the environment variable key */
    private string $key;

    /** Cache for validated default */
    private bool $defaultValidated = false;
    private mixed $validatedDefault = null;

    public function __construct(string $key, ?CasterInterface $caster = null)
    {
        $this->key = $key;
        if ($caster !== null) {
            $this->caster = $caster;
        }
    }

    /** Mark the variable as not required */
    public function optional(): static
    {
        $this->required = false;

        return $this;
    }

    /**
     * Define a default value and mark the variable as optional.
     */
    public function default(mixed $value): static
    {
        $this->hasDefault = true;
        $this->default = $value;
        $this->required = false;
        $this->invalidateDefaultCache();

        return $this;
    }

    /** Assign a caster to transform the raw value */
    public function setCaster(CasterInterface $caster): static
    {
        $this->caster = $caster;
        $this->invalidateDefaultCache();

        return $this;
    }

    /** Add a validation constraint */
    public function addConstraint(ConstraintInterface $constraint): static
    {
        $this->constraints[] = $constraint;
        $this->invalidateDefaultCache();

        return $this;
    }

    // ---- Meta getters ------------------------------------------------------

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function hasDefault(): bool
    {
        return $this->hasDefault;
    }

    public function getDefault(): mixed
    {
        return $this->default;
    }

    public function key(): string
    {
        return $this->key;
    }

    // ---- Apply -------------------------------------------------------------

    /**
     * Apply the caster and all constraints.
     * Cast only when the raw value is a string; otherwise only constraints are applied.
     */
    public function apply(mixed $raw): mixed
    {
        if ($this->isMissingRaw($raw)) {
            return $this->resolveMissing();
        }

        // Delegate both string and non-string inputs to the unified pipeline.
        // Strings will be cast first; non-strings will skip casting and only run constraints.
        return $this->validatePipeline($raw);
    }

    /**
     * Handle missing input: either throw (if required), return null, or return cached validated default.
     */
    private function resolveMissing(): mixed
    {
        // Fast path if no default is set
        if (! $this->hasDefault) {
            if ($this->required) {
                throw new ConstraintException(sprintf('ENV %s is required but missing', $this->key));
            }
            return null;
        }

        // Default present: validate once and cache
        if (! $this->defaultValidated) {
            $this->validatedDefault = $this->validatePipeline($this->default);
            $this->defaultValidated = true;
        }

        return $this->validatedDefault;
    }

    /**
     * Run casting (only if string) and constraints.
     */
    private function validatePipeline(mixed $value): mixed
    {
        if (is_string($value)) {
            $this->caster ??= new Caster\StringCaster();
            $value = $this->caster->cast($this->key, $value);
        }

        return $this->runConstraints($value);
    }

    /**
     * Run only constraints.
     */
    private function runConstraints(mixed $value): mixed
    {
        foreach ($this->constraints as $c) {
            $value = $c->assert($this->key, $value);
        }

        return $value;
    }

    /**
     * A value is considered "missing" if:
     * - null
     * - an empty string
     * - an empty array or empty Countable
     */
    private function isMissingRaw(mixed $raw): bool
    {
        if ($raw === null) {
            return true;
        }

        if (is_string($raw)) {
            return $raw === '';
        }

        if (is_array($raw) || $raw instanceof \Countable) {
            return count($raw) === 0;
        }

        return false;
    }

    /** Reset the validated default cache */
    private function invalidateDefaultCache(): void
    {
        $this->defaultValidated = false;
        $this->validatedDefault = null;
    }
}
