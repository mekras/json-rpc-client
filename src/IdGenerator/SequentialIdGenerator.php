<?php

/**
 * JSON-RPC Client.
 *
 * @copyright Михаил Красильников <m.krasilnikov@yandex.ru>
 */

declare(strict_types=1);

namespace Mekras\JsonRpcClient\IdGenerator;

/**
 * Sequential IDs generator.
 *
 * @since 1.0
 */
class SequentialIdGenerator implements IdGenerator
{
    /**
     * Next request ID.
     *
     * @var int
     */
    private $nextId;

    /**
     * Create new generator.
     *
     * @param int $firstId
     *
     * @since 1.0
     */
    public function __construct(int $firstId = 1)
    {
        $this->nextId = $firstId;
    }

    /**
     * Generate next request ID.
     *
     * @return string
     *
     * @since 1.0
     */
    public function generateId(): string
    {
        return (string) ($this->nextId++);
    }
}
