<?php

declare(strict_types=1);

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Phithi92\TypedEnv\Contracts;

use Phithi92\TypedEnv\Exception\HandlerException;

interface HandlerInterface
{
    /**
     * @throws HandlerException
     */
    public function __construct(mixed $value);

    public function rewind(): void;

    public function current(): int;

    /**
     * @throws HandlerException
     */
    public function write(string $value): void;

    /**
     * @throws HandlerException
     */
    public function read(): ?string;

    public function hasMore(): bool;
}
