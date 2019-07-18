<?php

/**
 * JSON-RPC Client.
 *
 * @copyright Михаил Красильников <m.krasilnikov@yandex.ru>
 */

declare(strict_types=1);

namespace Mekras\JsonRpcClient;

/**
 * JSON-RPC error code reference.
 *
 * @since 1.0
 */
final class Errors
{
    /**
     * Internal JSON-RPC error.
     */
    public const INTERNAL_ERROR = -32603;

    /**
     * Invalid method parameter(s).
     */
    public const INVALID_PARAMS = -32602;

    /**
     * The JSON sent is not a valid Request object.
     */
    public const INVALID_REQUEST = -32600;

    /**
     * The method does not exist / is not available.
     */
    public const METHOD_NOT_FOUND = -32601;

    /**
     * Invalid JSON was received by the server.
     */
    public const PARSE_ERROR = -32700;
}
