<?php

/**
 * JSON-RPC Client.
 *
 * @copyright Михаил Красильников <m.krasilnikov@yandex.ru>
 */

declare(strict_types=1);

namespace Mekras\JsonRpc\Exception\Server;

use Mekras\JsonRpc\Exception\RuntimeException;

/**
 * An exception on the server side.
 *
 * @since 1.0
 */
abstract class ServerException extends RuntimeException
{
}
