<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Handler;

use Phithi92\TypedEnv\Contracts\HandlerInterface;
use Phithi92\TypedEnv\Exception\HandlerException;

class MemoryHandler implements HandlerInterface
{
    /** @var array<string> $lines */
    private array $lines = [];
    private int $currentLine = 0;

    /**
     * @param array<string> $value
     *
     * @throws HandlerException
     */
    #[\Override]
    public function __construct(mixed $value)
    {
        if (! is_array($value)) {
            throw new HandlerException('Value must be an array for ArrayHandler.');
        }

        $this->lines = array_values($value); // Normalisiert die Keys auf 0,1,2,...
    }

    public function rewind(): void
    {
        $this->currentLine = 0;
    }

    public function current(): int
    {
        return $this->currentLine;
    }

    #[\Override]
    public function write(string $value): void
    {
        $this->lines[] = $value;
    }

    #[\Override]
    public function read(): string|null
    {
        if (! $this->hasMore()) {
            return null;
        }

        return $this->lines[$this->currentLine++];
    }

    #[\Override]
    public function hasMore(): bool
    {
        return $this->currentLine < count($this->lines);
    }
}
