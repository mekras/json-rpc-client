<?php

/**
 * JSON-RPC Client.
 *
 * @copyright Михаил Красильников <m.krasilnikov@yandex.ru>
 */

declare(strict_types=1);

namespace Mekras\JsonRpc;

use Psr\Http\Client\ClientInterface;

/**
 * JSON-RPC Client.
 *
 * @since 1.0
 */
class Client
{
    /**
     * API root URL.
     *
     * @var string
     */
    private $apiRoot;

    /**
     * HTTP client.
     *
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * Create new JSON-RPC client.
     *
     * @param string          $apiRoot    API root URL.
     * @param ClientInterface $httpClient HTTP client.
     *
     * @since 1.0
     */
    public function __construct(string $apiRoot, ClientInterface $httpClient)
    {
        // assert($apiRoot !== '', 'API root URL cannot be blank.');
        //
        // $this->httpClient = $httpClient;
        // $this->apiRoot    = $apiRoot;
    }

    /**
     * Call remote method.
     *
     * @param string $method RPC method name.
     * @param array  $params Method arguments.
     *
     * @return mixed
     *
     * @since 1.0
     */
    public function call(string $method, array $params)
    {
    }

    /**
     * Send notification.
     *
     * @param string $method RPC method name.
     * @param array  $params Method arguments.
     *
     * @since 1.0
     */
    public function notify(string $method, array $params): void
    {
    }
}
