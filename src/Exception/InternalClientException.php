<?php

/**
 * JSON-RPC Client.
 *
 * @copyright Михаил Красильников <m.krasilnikov@yandex.ru>
 */

declare(strict_types=1);

namespace Mekras\JsonRpcClient\Exception;


/**
 * Error in library logic.
 *
 * @since 1.0
 */
class InternalClientException extends LogicException
{
}
