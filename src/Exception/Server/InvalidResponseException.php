<?php

/**
 * JSON-RPC Client.
 *
 * @copyright Михаил Красильников <m.krasilnikov@yandex.ru>
 */

declare(strict_types=1);

namespace Mekras\JsonRpcClient\Exception\Server;

/**
 * Invalid response from RPC server.
 *
 * @since 1.0
 */
class InvalidResponseException extends ServerException
{
}
