<?php

/**
 * JSON-RPC Client.
 *
 * @copyright Михаил Красильников <m.krasilnikov@yandex.ru>
 */

declare(strict_types=1);

namespace Mekras\JsonRpc\Exception\Client;


/**
 * Invalid JSON was received by the server.
 *
 * An error occurred on the server while parsing the JSON text.
 *
 * @since 1.0
 */
class ParseErrorException extends ClientException
{
}
