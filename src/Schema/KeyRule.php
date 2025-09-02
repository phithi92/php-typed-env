<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Schema;

use Phithi92\TypedEnv\Caster;
use Phithi92\TypedEnv\Contracts\CasterInterface;
use Phithi92\TypedEnv\Contracts\ConstraintInterface;
use Phithi92\TypedEnv\Exception\ConstraintException;

/**
 * KeyRule with fluent configuration and runtime apply().
 * - Constructor accepts only the key.
 * - Provides type*() methods.
 * - apply() casts only when input is a string; otherwise runs constraints.
 */
abstract class KeyRule
{
    /** Optional caster used to convert raw values to the expected type */
    private ?CasterInterface $caster = null;

    /** @var list<ConstraintInterface> List of validation constraints applied to the value */
    private array $constraints = [];

    /** Indicates whether the environment variable is required */
    private bool $required = true;

    /** Indicates whether a default value has been explicitly set */
    private bool $hasDefault = false;

    /** Holds the default value (typed or untyped) */
    private mixed $default = null;

    /** Name of the environment variable key */
    private string $key;

    /**
     * Initialize the rule for a specific environment key.
     *
     * @param string $key Name of the environment variable.
     */
    public function __construct(string $key, ?CasterInterface $caster = null)
    {
        $this->key = $key;

        if ($caster !== null) {
            $this->caster = $caster;
        }
    }

    /**
     * Mark the environment variable as not required.
     */
    public function optional(): static
    {
        $this->required = false;
        return $this;
    }

    /**
     * Define a default value and mark the variable as optional.
     *
     * @param mixed $value Default value to use when variable is missing.
     */
    public function default(mixed $value): static
    {
        $this->hasDefault = true;
        $this->default = $value;
        $this->required = false;
        return $this;
    }

    /**
     * Assign a caster to transform the raw value.
     *
     * @param CasterInterface $caster Caster responsible for type conversion.
     */
    public function setCaster(CasterInterface $caster): static
    {
        $this->caster = $caster;
        return $this;
    }

    /**
     * Append a validation constraint.
     *
     * @param ConstraintInterface $constraint Constraint to apply.
     */
    public function addConstraint(ConstraintInterface $constraint): static
    {
        $this->constraints[] = $constraint;
        return $this;
    }

    // ---- Meta getters ------------------------------------------------------

    /**
     * Determine if the variable is required.
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * Check whether a default value is set.
     */
    public function hasDefault(): bool
    {
        return $this->hasDefault;
    }

    /**
     * Retrieve the default value.
     */
    public function getDefault(): mixed
    {
        return $this->default;
    }

    /**
     * Get the environment variable name.
     */
    public function key(): string
    {
        return $this->key;
    }

    // ---- Apply -------------------------------------------------------------

    /**
     * Apply the caster and all constraints.
     * Cast only when the raw value is a string; otherwise only constraints are applied.
     *
     * @param mixed $raw Raw environment value.
     *
     * @return mixed Cast and validated value.
     */
    public function apply(mixed $raw): mixed
    {
        $isMissing = $this->isMissingRaw($raw);
        if ($isMissing) {
            if ($this->hasDefault) {
                return $this->default;
            }
            if ($this->required) {
                throw new ConstraintException(sprintf('ENV %s is required but missing', $this->key));
            }
            return null;
        }

        if (is_string($raw)) {
            // Fallback to StringCaster if no caster was set
            $this->caster ??= new Caster\StringCaster();

            $value = $this->caster->cast($this->key, $raw);
        } else {
            // Already typed default or previously validated value
            $value = $raw;
        }

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
}
