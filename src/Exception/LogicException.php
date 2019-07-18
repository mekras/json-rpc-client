<?php

/**
 * JSON-RPC Client.
 *
 * @copyright Михаил Красильников <m.krasilnikov@yandex.ru>
 */

declare(strict_types=1);

namespace Mekras\JsonRpcClient\Exception;


/**
 * Base class for all logic exceptions.
 *
 * @since 1.0
 */
abstract class LogicException extends \LogicException implements JsonRpcClientException
{
}
