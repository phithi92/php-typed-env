<?php

namespace Phithi92\TypedEnv\Tests;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Schema\Schema;
use Phithi92\TypedEnv\Schema\KeyRule;
use Phithi92\TypedEnv\EnvKit;
use Phithi92\TypedEnv\DotenvParser;
use Phithi92\TypedEnv\Handler\MemoryHandler;
use Phithi92\TypedEnv\Exception\ConstraintException;

final class SchemaTest extends TestCase
{
    public function testBuildReturnsSchemaInstance(): void
    {
        $schema = Schema::build();
        $this->assertInstanceOf(Schema::class, $schema);
    }

    public function testListWithToManyItems(): void
    {
        $e = new EnvKit(new DotenvParser(new MemoryHandler([
            'LIST=cheese,love,cake'
        ])));

        // ---- Test with to many items -------------------------------------
        $schema = Schema::build();
        $schema->list('LIST')
            ->maxItems(2)
            ->minItems(1);
        $this->expectException(ConstraintException::class);
        $e->validate($schema);
    }

    public function testListWithToFewItems(): void
    {
        $e = new EnvKit(new DotenvParser(new MemoryHandler([
            'LIST=cheese'
        ])));

        // ---- Test with to many items -------------------------------------
        $schema = Schema::build();
        $schema->list('LIST')
            ->maxItems(2)
            ->minItems(2);
        $this->expectException(ConstraintException::class);
        $e->validate($schema);
    }

    public function testListWithEmptyItem(): void
    {
        $e = new EnvKit(new DotenvParser(new MemoryHandler([
            'LIST=cheese,,'
        ])));

        // ---- Test with to many items -------------------------------------
        $schema = Schema::build();
        $schema->list('LIST')
            ->assertValuesNotEmpty();
        $this->expectException(ConstraintException::class);
        $e->validate($schema);
    }


    public function testTypedEntryPointsReturnKeyRuleAndStoreIt(): void
    {
        $schema = Schema::build();

        $r1 = $schema->string('APP_ENV');
        $r2 = $schema->bool('DEBUG');
        $r3 = $schema->int('PORT');
        $r4 = $schema->float('RATIO');
        $r5 = $schema->duration('TIMEOUT');
        $r6 = $schema->json('CONFIG');
        $r7 = $schema->list('HOSTS');
        $r8 = $schema->uuid('REQUEST_ID');
        $r9 = $schema->uuid('TRACE_ID', true);
        $r10 = $schema->size('UPLOAD_LIMIT');
        $r11 = $schema->port('EXPOSED_PORT');

        $this->assertInstanceOf(KeyRule::class, $r1);
        $this->assertInstanceOf(KeyRule::class, $r2);
        $this->assertInstanceOf(KeyRule::class, $r3);
        $this->assertInstanceOf(KeyRule::class, $r4);
        $this->assertInstanceOf(KeyRule::class, $r5);
        $this->assertInstanceOf(KeyRule::class, $r6);
        $this->assertInstanceOf(KeyRule::class, $r7);
        $this->assertInstanceOf(KeyRule::class, $r8);
        $this->assertInstanceOf(KeyRule::class, $r9);
        $this->assertInstanceOf(KeyRule::class, $r10);
        $this->assertInstanceOf(KeyRule::class, $r11);

        $all = $schema->all();

        $this->assertArrayHasKey('APP_ENV', $all);
        $this->assertArrayHasKey('DEBUG', $all);
        $this->assertArrayHasKey('PORT', $all);
        $this->assertArrayHasKey('RATIO', $all);
        $this->assertArrayHasKey('TIMEOUT', $all);
        $this->assertArrayHasKey('CONFIG', $all);
        $this->assertArrayHasKey('HOSTS', $all);
        $this->assertArrayHasKey('REQUEST_ID', $all);
        $this->assertArrayHasKey('TRACE_ID', $all);
        $this->assertArrayHasKey('UPLOAD_LIMIT', $all);
        $this->assertArrayHasKey('EXPOSED_PORT', $all);

        $this->assertSame($r1, $all['APP_ENV']);
        $this->assertSame($r2, $all['DEBUG']);
        $this->assertSame($r3, $all['PORT']);
        $this->assertSame($r4, $all['RATIO']);
        $this->assertSame($r5, $all['TIMEOUT']);
        $this->assertSame($r6, $all['CONFIG']);
        $this->assertSame($r7, $all['HOSTS']);
        $this->assertSame($r8, $all['REQUEST_ID']);
        $this->assertSame($r9, $all['TRACE_ID']);
        $this->assertSame($r10, $all['UPLOAD_LIMIT']);
        $this->assertSame($r11, $all['EXPOSED_PORT']);
    }

    public function testDifferentKeysCreateDifferentRuleInstances(): void
    {
        $schema = Schema::build();

        $a = $schema->string('A');
        $b = $schema->string('B');

        $this->assertNotSame($a, $b);
        $all = $schema->all();
        $this->assertCount(2, $all);
        $this->assertSame($a, $all['A']);
        $this->assertSame($b, $all['B']);
    }

    public function testDateTimeEntryPointReturnsKeyRuleAndStoresKey(): void
    {
        $schema = Schema::build();

        $rule = $schema->dateTime('START_AT', 'Y-m-d H:i:s', true);
        $this->assertInstanceOf(KeyRule::class, $rule);

        $all = $schema->all();
        $this->assertArrayHasKey('START_AT', $all);
        $this->assertSame($rule, $all['START_AT']);
    }

    public function testFluentChainingFromTypedEntryPoint(): void
    {
        $schema = Schema::build();

        // We only test chaining works on the returned KeyRule object; we don't execute external validations.
        $rule = $schema->int('PORT')->min(1000)->max(9999)->optional()->default(8080);
        $this->assertInstanceOf(KeyRule::class, $rule);

        $all = $schema->all();
        $this->assertArrayHasKey('PORT', $all);
        $this->assertSame($rule, $all['PORT']);
    }
}
