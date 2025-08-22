<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Phithi92\TypedEnv\EnvKit;
use Phithi92\TypedEnv\Schema;

$schema = Schema::build()
    ->string('APP_ENV')
    ->bool('DEBUG')
    ->int('DB_PORT', 1024, 65535)
    ->string('REDIS_URL')
    ->duration('REQUEST_TIMEOUT');

$config = (new EnvKit())
    ->loadDotenv(__DIR__ . '/../.env')
    ->validate($schema);

print_r($config->all());
