<?php

/**
 * JSON-RPC Client.
 *
 * @copyright Михаил Красильников <m.krasilnikov@yandex.ru>
 */

declare(strict_types=1);

namespace Mekras\JsonRpcClient\Exception;

/**
 * Common interface for all JSON-RPC client library exceptions.
 *
 * @since 1.0
 */
interface JsonRpcClientException extends \Throwable
{
}
