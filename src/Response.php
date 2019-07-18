<?php

/**
 * JSON-RPC Client.
 *
 * @copyright Михаил Красильников <m.krasilnikov@yandex.ru>
 */

declare(strict_types=1);

namespace Mekras\JsonRpcClient;

/**
 * JSON-RPC response object.
 *
 * @since 1.0
 */
final class Response
{
    /**
     * Request identifier.
     *
     * @var string
     */
    private $id;

    /**
     * Method result.
     *
     * @var mixed
     */
    private $result;

    /**
     * Create new response.
     *
     * @param string $id     Request identifier.
     * @param mixed  $result The value is determined by the method invoked on the server.
     *
     * @since 1.0
     */
    public function __construct(string $id, $result)
    {
        $this->id = $id;
        $this->result = $result;
    }

    /**
     * Return identifier.
     *
     * @return string
     *
     * @since 1.0
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Return method result.
     *
     * @return mixed
     *
     * @since 1.0
     */
    public function getResult()
    {
        return $this->result;
    }
}
