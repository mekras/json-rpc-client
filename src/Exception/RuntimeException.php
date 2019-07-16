<?php

/**
 * JSON-RPC Client.
 *
 * @copyright Михаил Красильников <m.krasilnikov@yandex.ru>
 */

declare(strict_types=1);

namespace Mekras\JsonRpc\Exception;


/**
 * Base class for all runtime exceptions.
 *
 * @since 1.0
 */
abstract class RuntimeException extends \RuntimeException implements JsonRpcClientException
{
}
