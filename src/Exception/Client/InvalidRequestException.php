<?php

/**
 * JSON-RPC Client.
 *
 * @copyright Михаил Красильников <m.krasilnikov@yandex.ru>
 */

declare(strict_types=1);

namespace Mekras\JsonRpc\Exception\Client;


/**
 * The JSON sent is not a valid Request object.
 *
 * @since 1.0
 */
class InvalidRequestException extends ClientException
{
}
