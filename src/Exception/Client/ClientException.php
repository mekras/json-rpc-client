<?php

/**
 * JSON-RPC Client.
 *
 * @copyright Михаил Красильников <m.krasilnikov@yandex.ru>
 */

declare(strict_types=1);

namespace Mekras\JsonRpcClient\Exception\Client;

use Mekras\JsonRpcClient\Exception\LogicException;

/**
 * An exception on the client side.
 *
 * @since 1.0
 */
class ClientException extends LogicException
{
}
