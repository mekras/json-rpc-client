# Клиент JSON-RPC

## Установка

    composer require mekras/json-rpc-client

## Использование

```php
<?php

use Mekras\JsonRpcClient\Client;
use Mekras\JsonRpcClient\Exception\JsonRpcClientException;
use Mekras\JsonRpcClient\Request;

/** @var Psr\Http\Client\ClientInterface $httpClient */
/** @var Psr\Http\Message\RequestFactoryInterface $httpRequestFactory */
/** @var Psr\Http\Message\StreamFactoryInterface $httpStreamFactory */

$client = new Client('http://example.com/rpc', $httpClient, $httpRequestFactory, $httpStreamFactory);

$request = new Request('rpc_method', ['foo' => 'bar'], '12345');

try {
    $response = $client->sendRequest($request);
} catch (JsonRpcClientException $exception) {
    printf("RPC request failed: %s\n", $exception->getMessage());
}

print_r($response->getResult());
```
