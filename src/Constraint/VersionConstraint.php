<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use Phithi92\TypedEnv\Contracts\ConstraintInterface;
use Phithi92\TypedEnv\Exception\ConstraintException;

/**
 * Prüft eine Versionsbedingung anhand eines Operators.
 *
 * Unterstützte Operatoren:
 *  - '>', '>=', '<', '<=', '==', '=', '!='
 * Beispiel:
 *  new VersionConstraint('>=', '1.2.3')
 */
final class VersionConstraint implements ConstraintInterface
{
    private string $operator;
    private string $version;

    public function __construct(string $operator, string $version)
    {
        $operator = trim($operator);
        if (! in_array($operator, ['>', '<', '>=', '<=', '==', '=', '!='], true)) {
            throw new \InvalidArgumentException(sprintf('Unsupported operator "%s".', $operator));
        }

        if (! self::isValidVersion($version)) {
            throw new \InvalidArgumentException(sprintf('Invalid version "%s".', $version));
        }

        $this->operator = $operator === '=' ? '==' : $operator;
        $this->version = $version;
    }

    public function assert(string $key, mixed $value): mixed
    {
        if (! is_string($value)) {
            throw new ConstraintException(sprintf('ENV %s: value is not a string', $key));
        }

        if (! self::isValidVersion($value)) {
            throw new ConstraintException(sprintf('ENV %s: "%s" is not a valid version', $key, $value));
        }

        $cmp = version_compare($value, $this->version);

        switch ($this->operator) {
            case '>':
                if ($cmp <= 0) {
                    throw new ConstraintException(
                        sprintf(
                            'ENV %s: version %s <= min %s',
                            $key,
                            $value,
                            $this->version
                        )
                    );
                }
                break;

            case '>=':
                if ($cmp < 0) {
                    throw new ConstraintException(
                        sprintf(
                            'ENV %s: version %s < min %s',
                            $key,
                            $value,
                            $this->version
                        )
                    );
                }
                break;

            case '<':
                if ($cmp >= 0) {
                    throw new ConstraintException(
                        sprintf(
                            'ENV %s: version %s >= max %s',
                            $key,
                            $value,
                            $this->version
                        )
                    );
                }
                break;

            case '<=':
                if ($cmp > 0) {
                    throw new ConstraintException(
                        sprintf(
                            'ENV %s: version %s > max %s',
                            $key,
                            $value,
                            $this->version
                        )
                    );
                }
                break;

            case '==':
                if ($cmp !== 0) {
                    throw new ConstraintException(
                        sprintf(
                            'ENV %s: version %s != expected %s',
                            $key,
                            $value,
                            $this->version
                        )
                    );
                }
                break;

            case '!=':
                if ($cmp === 0) {
                    throw new ConstraintException(
                        sprintf(
                            'ENV %s: version %s == disallowed %s',
                            $key,
                            $value,
                            $this->version
                        )
                    );
                }
                break;
        }

        return $value;
    }

    /**
     * Einfache Gültigkeitsprüfung für SemVer-ähnliche Strings:
     * - mind. MAJOR.MINOR.PATCH
     * - optionale Prä-/Build-Tags erlaubt (z. B. -rc.1, +meta)
     */
    private static function isValidVersion(string $v): bool
    {
        return (bool) preg_match(
            '/^\d+\.\d+\.\d+(?:-[0-9A-Za-z.-]+)?(?:\+[0-9A-Za-z.-]+)?$/',
            $v
        );
    }
}
