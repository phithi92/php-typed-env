<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Types;

use Phithi92\TypedEnv\Caster\LocaleCaster;
use Phithi92\TypedEnv\Constraint\EnumConstraint;
use Phithi92\TypedEnv\Schema\KeyRule;

/**
 * Rule for environment variables representing locale strings.
 *
 * Examples of valid values:
 *  - "en"
 *  - "en_US"
 *  - "de_DE"
 *  - "fr_FR.UTF-8"
 */
final class LocaleKeyRule extends KeyRule
{
    public function __construct(string $key)
    {
        parent::__construct($key, new LocaleCaster());
    }

    /**
     * Restrict the locale to a specific set of allowed values.
     *
     * @param list<string> $locales
     */
    public function allowed(array $locales): LocaleKeyRule
    {
        return $this->addConstraint(new EnumConstraint($locales));
    }
}
