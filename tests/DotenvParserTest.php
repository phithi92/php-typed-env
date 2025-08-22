<?php

namespace Phithi92\TypedEnv\Tests;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\DotenvParser;
use Phithi92\TypedEnv\DotenvParserConfig;
use Phithi92\TypedEnv\Exception\DotenvFileException;
use Phithi92\TypedEnv\Exception\DotenvSyntaxException;

final class DotenvParserTest extends TestCase
{
    /** @var list<string> */
    private array $tmpFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->tmpFiles as $f) {
            @unlink($f);
        }
        $this->tmpFiles = [];
    }

    private function writeTemp(string $content): string
    {
        $p = tempnam(sys_get_temp_dir(), 'dotenv_');
        file_put_contents($p, $content);
        $this->tmpFiles[] = $p;
        return $p;
    }

    public function testParsesBasicLinesOnly(): void
    {
        $path = $this->writeTemp(<<<'ENV'
            APP_ENV=dev
            DEBUG=1
            DB_PORT=5432
            # comment line

            REDIS_URL=redis://localhost:6379
            REQUEST_TIMEOUT=5s
            ENV);

        $parser = new DotenvParser(); // default config
        $data = $parser->parse($path);

        $this->assertSame([
            'APP_ENV'         => 'dev',
            'DEBUG'           => '1',
            'DB_PORT'         => '5432',
            'REDIS_URL'       => 'redis://localhost:6379',
            'REQUEST_TIMEOUT' => '5s',
        ], $data);
    }

    public function testExportPrefixHandledOnlyWhenEnabled(): void
    {
        $path = $this->writeTemp("export FOO=bar\n   export   BAR= baz\n");

        // aktiviert: export wird entfernt
        $parser = new DotenvParser(new DotenvParserConfig(allowExport: true));
        $data   = $parser->parse($path);
        $this->assertSame('bar', $data['FOO']);
        $this->assertSame('baz', $data['BAR']);

        // deaktiviert: export bleibt Teil des Keys (reines Parser-Verhalten)
        $parserNo = new DotenvParser(new DotenvParserConfig(allowExport: false));
        $raw      = $parserNo->parse($path);
        $this->assertArrayHasKey('export FOO', $raw);
        $this->assertSame('bar', $raw['export FOO']);
    }

    public function testInlineCommentsRemovedOnlyWhenEnabledAndNotQuoted(): void
    {
        $path = $this->writeTemp(<<<'ENV'
            RAW=foo # trailing
            QUOTED="foo # not a comment"
            SINGLE='bar # also not a comment'
            ENV);

        // aktiviert: trailing # nach value (ohne quotes) wird entfernt
        $parser = new DotenvParser(new DotenvParserConfig(allowInlineComments: true));
        $data   = $parser->parse($path);
        $this->assertSame('foo', $data['RAW']);
        $this->assertSame('foo # not a comment', $data['QUOTED']); // quotes bleiben Inhalt
        $this->assertSame('bar # also not a comment', $data['SINGLE']);

        // deaktiviert: nichts wird abgeschnitten
        $parserOff = new DotenvParser(new DotenvParserConfig(allowInlineComments: false));
        $raw       = $parserOff->parse($path);
        $this->assertSame('foo # trailing', $raw['RAW']);
    }

    public function testStripsUtf8BomAndKeepsCrLf(): void
    {
        // BOM + CRLF
        $path = $this->writeTemp("\xEF\xBB\xBFFOO=bar\r\nBAR=baz\r\n");
        $parser = new DotenvParser();
        $data   = $parser->parse($path);

        $this->assertSame('bar', $data['FOO']);
        $this->assertSame('baz', $data['BAR']);
    }

    public function testThrowsOnMissingEquals(): void
    {
        $path = $this->writeTemp("INVALID_LINE\nFOO=bar\n");

        $this->expectException(DotenvSyntaxException::class);
        (new DotenvParser())->parse($path);
    }

    public function testThrowsOnEmptyKey(): void
    {
        $path = $this->writeTemp("=value\n");

        $this->expectException(DotenvSyntaxException::class);
        (new DotenvParser())->parse($path);
    }

    public function testThrowsIfFileNotFound(): void
    {
        $this->expectException(DotenvFileException::class);
        (new DotenvParser())->parse(__DIR__ . '/not_exists.env');
    }
}
