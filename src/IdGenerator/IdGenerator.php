<?php

/**
 * JSON-RPC Client.
 *
 * @copyright Михаил Красильников <m.krasilnikov@yandex.ru>
 */

declare(strict_types=1);

namespace Mekras\JsonRpcClient\IdGenerator;

/**
 * RPC request ID generator.
 *
 * @since 1.0
 */
interface IdGenerator
{
    /**
     * Generate next request ID.
     *
     * @return string
     *
     * @since 1.0
     */
    public function generateId(): string;
}
