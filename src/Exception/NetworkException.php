<?php

/**
 * JSON-RPC Client.
 *
 * @copyright Михаил Красильников <m.krasilnikov@yandex.ru>
 */

declare(strict_types=1);

namespace Mekras\JsonRpcClient\Exception;


/**
 * Request cannot be completed because of network issues.
 *
 * @since 1.0
 */
class NetworkException extends RuntimeException
{
}
