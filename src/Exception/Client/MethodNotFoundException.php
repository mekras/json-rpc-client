<?php

/**
 * JSON-RPC Client.
 *
 * @copyright Михаил Красильников <m.krasilnikov@yandex.ru>
 */

declare(strict_types=1);

namespace Mekras\JsonRpc\Exception\Client;


/**
 * The method does not exist / is not available.
 *
 * @since 1.0
 */
class MethodNotFoundException extends ClientException
{
}
