<?php

/**
 * JSON-RPC Client.
 *
 * @copyright Михаил Красильников <m.krasilnikov@yandex.ru>
 */

declare(strict_types=1);

namespace Mekras\JsonRpc\Exception\Client;

use Mekras\JsonRpc\Exception\LogicException;

/**
 * An exception on the client side.
 *
 * @since 1.0
 */
abstract class ClientException extends LogicException
{
}
