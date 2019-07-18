<?php

/**
 * JSON-RPC Client.
 *
 * @copyright Михаил Красильников <m.krasilnikov@yandex.ru>
 */

declare(strict_types=1);

namespace Mekras\JsonRpcClient\Exception\Server;

use Mekras\JsonRpcClient\Exception\RuntimeException;

/**
 * An exception on the server side.
 *
 * @since 1.0
 */
abstract class ServerException extends RuntimeException
{
}
