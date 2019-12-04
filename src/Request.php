<?php

/**
 * JSON-RPC Client.
 *
 * @copyright Михаил Красильников <m.krasilnikov@yandex.ru>
 */

declare(strict_types=1);

namespace Mekras\JsonRpcClient;

/**
 * JSON-RPC request object.
 *
 * @since 1.0
 */
final class Request
{
    /**
     * Request identifier.
     *
     * @var string|null
     */
    private $id;

    /**
     * Name of the method to be invoked.
     *
     * @var string
     */
    private $method;

    /**
     * Parameter values to be used during the invocation of the method.
     *
     * @var array<mixed>
     */
    private $params;

    /**
     * Create new request.
     *
     * @param string       $method Name of the method to be invoked.
     * @param array<mixed> $params Parameter values to be used during the invocation of the method.
     * @param string|null  $id     Request identifier. If it is not included it is assumed to be a
     *                             notification.
     *
     * @since 1.0
     */
    public function __construct(string $method, array $params = [], string $id = null)
    {
        $this->id = $id;
        $this->method = $method;
        $this->params = $params;
    }

    /**
     * Return identifier.
     *
     * @return string|null
     *
     * @since 1.0
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Return name of the method to be invoked.
     *
     * @return string
     *
     * @since 1.0
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Return parameter values.
     *
     * @return array<mixed>
     *
     * @since 1.0
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Return copy of the request with named parameter.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return self
     *
     * @since 1.0
     */
    public function withNamedParameter(string $name, $value): self
    {
        $clone = clone $this;
        $clone->params[$name] = $value;

        return $clone;
    }

    /**
     * Return copy of the request with added positional parameter.
     *
     * @param mixed $value
     *
     * @return self
     *
     * @since 1.0
     */
    public function withParameter($value): self
    {
        $clone = clone $this;
        $clone->params[] = $value;

        return $clone;
    }
}
