<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv;

use Phithi92\TypedEnv\Contracts\CasterInterface;
use Phithi92\TypedEnv\Contracts\ConstraintInterface;

/**
 * KeyRule with fluent configuration and runtime apply().
 * - Constructor accepts only the key.
 * - Provides type*() methods.
 * - apply() casts only when input is a string; otherwise runs constraints.
 */
final class KeyRule
{
    // Default delimiter for list values (comma-separated)
    private const LIST_DELIMITER = ',';

    // Default format for DateTime values (ISO 8601)
    private const DATETIME_FORMAT = 'c';

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

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function optional(): self
    {
        $this->required = false;
        return $this;
    }

    public function default(mixed $value): self
    {
        $this->hasDefault = true;
        $this->default = $value;
        $this->required = false;
        return $this;
    }

    public function setCaster(CasterInterface $caster): self
    {
        $this->caster = $caster;
        return $this;
    }

    public function addConstraint(ConstraintInterface $constraint): self
    {
        $this->constraints[] = $constraint;
        return $this;
    }

    // ---- Type shortcuts ----------------------------------------------------

    public function typeString(): self
    {
        return $this->setCaster(new Caster\StringCaster());
    }

    public function typeBool(): self
    {
        return $this->setCaster(new Caster\BoolCaster());
    }

    public function typeInt(): self
    {
        return $this->setCaster(new Caster\IntCaster());
    }

    public function typeFloat(): self
    {
        return $this->setCaster(new Caster\FloatCaster());
    }

    public function typeDuration(): self
    {
        return $this->setCaster(new Caster\DurationCaster());
    }

    public function typeJson(bool $assoc = true): self
    {
        return $this->setCaster(new Caster\JsonCaster($assoc));
    }

    public function typeList(string $d = self::LIST_DELIMITER, bool $allowEmpty = false): self
    {
        return $this->setCaster(new Caster\ListCaster($d, $allowEmpty));
    }

    public function typeUrl(): self
    {
        return $this->setCaster(new Caster\UrlCaster());
    }

    public function typeEmail(): self
    {
        return $this->setCaster(new Caster\EmailCaster());
    }

    public function typeIp(): self
    {
        return $this->setCaster(new Caster\IpCaster());
    }

    public function typeUuidAny(): self
    {
        return $this->setCaster(new Caster\UuidAnyCaster());
    }

    public function typeUuidV4(): self
    {
        return $this->setCaster(new Caster\UuidV4Caster());
    }

    public function typeSize(): self
    {
        return $this->setCaster(new Caster\SizeCaster());
    }

    public function typePort(): self
    {
        return $this->setCaster(new Caster\PortCaster());
    }

    public function typeDate(string $format = 'Y-m-d'): self
    {
        return $this->setCaster(new Caster\DateCaster($format));
    }

    public function typeDateTime(string $fmt = self::DATETIME_FORMAT, bool $immutable = true): self
    {
        return $this->setCaster(new Caster\DateTimeCaster($fmt, $immutable));
    }

    public function typePath(bool $resolveRealpath = false): self
    {
        return $this->setCaster(new Caster\PathCaster($resolveRealpath));
    }

    public function typeChmod(): self
    {
        return $this->setCaster(new Caster\ChmodCaster());
    }

    public function typeHex(?int $length = null): self
    {
        return $this->setCaster(new Caster\HexCaster($length));
    }

    public function typeBase64(): self
    {
        return $this->setCaster(new Caster\Base64Caster());
    }

    public function typeNumericString(): self
    {
        return $this->setCaster(new Caster\NumericStringCaster());
    }

    public function typeRegex(string $pattern): self
    {
        return $this->setCaster(new Caster\RegexCaster($pattern));
    }

    public function typeLocale(): self
    {
        return $this->setCaster(new Caster\LocaleCaster());
    }

    public function typeColor(): self
    {
        return $this->setCaster(new Caster\ColorCaster());
    }

    public function typeUrlPath(): self
    {
        return $this->setCaster(new Caster\UrlPathCaster());
    }

    public function typeVersion(): self
    {
        return $this->setCaster(new Caster\VersionCaster());
    }

    public function typeArray(string $delimiter = ','): self
    {
        return $this->setCaster(new Caster\ArrayCaster($delimiter));
    }

    // ---- Caster shortcuts --------------------------------------------------

    public function min(int|float $m): self
    {
        return $this->addConstraint(new Constraint\MinConstraint($m));
    }

    public function max(int|float $m): self
    {
        return $this->addConstraint(new Constraint\MaxConstraint($m));
    }

    /**
     * @param list<bool|float|int|string> $a
     */
    public function enum(array $a): self
    {
        return $this->addConstraint(new Constraint\EnumConstraint($a));
    }

    public function pattern(string $r): self
    {
        return $this->addConstraint(new Constraint\PatternConstraint($r));
    }

    public function exists(): self
    {
        return $this->addConstraint(new Constraint\ExistsConstraint());
    }

    public function isFile(): self
    {
        return $this->addConstraint(new Constraint\IsFileConstraint());
    }

    public function isDir(): self
    {
        return $this->addConstraint(new Constraint\IsDirConstraint());
    }

    public function isReadable(): self
    {
        return $this->addConstraint(new Constraint\IsReadableConstraint());
    }

    public function isWritable(): self
    {
        return $this->addConstraint(new Constraint\IsWritableConstraint());
    }

    public function isExecutable(): self
    {
        return $this->addConstraint(new Constraint\IsExecutableConstraint());
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
}
