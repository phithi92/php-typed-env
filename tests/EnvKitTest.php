<?php

namespace Phithi92\TypedEnv\Tests;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\EnvKit;
use Phithi92\TypedEnv\Schema;

final class EnvKitTest extends TestCase
{
    /** @var list<string> */
    private array $tmp = [];

    protected function tearDown(): void
    {
        foreach ($this->tmp as $f) {
            @unlink($f);
        }
        $this->tmp = [];
        unset($_ENV['FALLBACK']);
    }

    private function writeEnv(string $content): string
    {
        $p = tempnam(sys_get_temp_dir(), 'env_');
        file_put_contents($p, $content);
        $this->tmp[] = $p;
        return $p;
    }

    public function testLoadAndValidate(): void
    {
        $path = $this->writeEnv(<<<'ENV'
APP_ENV=dev
DEBUG=1
PORT=5432
TIMEOUT=2m
ENV);

        // IMPORTANT: keep the Schema instance; call typed methods on it separately
        $schema = Schema::build();
        $schema->string('APP_ENV');
        $schema->bool('DEBUG');
        $schema->int('PORT', 1, 65535);
        $schema->duration('TIMEOUT');

        $cfg = (new EnvKit())
            ->loadDotenv($path)
            ->validate($schema);

        $this->assertSame('dev', $cfg->get('APP_ENV'));
        $this->assertTrue($cfg->get('DEBUG'));
        $this->assertSame(5432, $cfg->get('PORT'));
        $this->assertSame(120, $cfg->get('TIMEOUT')); // 2m => 120 sec
    }

    public function testThrowsIfMissingVariable(): void
    {
        $path = $this->writeEnv("");
        unset($_ENV['MISSING']);

        $schema = Schema::build();
        $schema->string('MISSING'); // required by default

        $this->expectException(\RuntimeException::class);
        (new EnvKit())->loadDotenv($path)->validate($schema);
    }

    public function testFallsBackToEnvSuperglobal(): void
    {
        $path = $this->writeEnv("APP_ENV=prod\n");
        $_ENV['FALLBACK'] = '42';

        $schema = Schema::build();
        $schema->string('APP_ENV');
        $schema->int('FALLBACK');

        $cfg = (new EnvKit())
            ->loadDotenv($path)
            ->validate($schema);

        $this->assertSame('prod', $cfg->get('APP_ENV'));
        $this->assertSame(42, $cfg->get('FALLBACK'));
    }
}
