<?php

declare(strict_types=1);

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Phithi92\TypedEnv\Types;

use Phithi92\TypedEnv\Caster\RegexCaster;
use Phithi92\TypedEnv\Schema\KeyRule;

/**
 * Description of RegexKeyRule
 *
 * @author phillipthiele
 */
class RegexKeyRule extends KeyRule
{
    public function __construct(string $key, string $pattern)
    {
        parent::__construct($key, new RegexCaster($pattern));
    }
}
