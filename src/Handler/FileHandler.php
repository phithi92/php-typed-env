<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Handler;

use Phithi92\TypedEnv\Contracts\HandlerInterface;
use Phithi92\TypedEnv\Exception\HandlerException;
use SplFileObject;

class FileHandler implements HandlerInterface
{
    private const DEFAULT_FILE_MODE = 'r';

    private const LINE_ENDING = "\r\n";
    private SplFileObject $file;

    private int $currentLine = 0;

    #[\Override]
    public function __construct(mixed $value)
    {
        if (! is_string($value) || ! is_file($value)) {
            throw new HandlerException('No valid path or file does not exist.');
        }

        try {
            $this->file = new SplFileObject($value, self::DEFAULT_FILE_MODE);
        } catch (\Exception $e) {
            throw new HandlerException('Unable to open file: ' . $e->getMessage());
        }
    }

    public function rewind(): void
    {
        $this->file->rewind();
        $this->currentLine = 0;
    }

    public function current(): int
    {
        return $this->currentLine;
    }

    #[\Override]
    public function write(string $value): void
    {
        $this->file->fwrite($value);
    }

    #[\Override]
    public function read(): string|null
    {
        if (! $this->hasMore()) {
            return null;
        }

        $line = $this->file->fgets();   // erst lesen
        $this->currentLine++;

        return $line !== false ? rtrim($line, self::LINE_ENDING) : false;
    }

    #[\Override]
    public function hasMore(): bool
    {
        return ! $this->file->eof();
    }
}
