<?php

declare(strict_types=1);

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Phithi92\TypedEnv\Contracts;

interface HandlerInterface
{
    public function __construct(mixed $value);

    public function rewind(): void;

    public function current(): int;

    public function write(string $value): void;

    public function read(): string|null;

    public function hasMore(): bool;
}
