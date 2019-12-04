<?php

/**
 * JSON-RPC client.
 *
 * @copyright Михаил Красильников <m.krasilnikov@yandex.ru>
 */

declare(strict_types=1);

namespace Mekras\JsonRpcClient;

use Mekras\JsonRpcClient\Exception\Client\ClientException;
use Mekras\JsonRpcClient\Exception\Client\InvalidParamsException;
use Mekras\JsonRpcClient\Exception\Client\InvalidRequestException;
use Mekras\JsonRpcClient\Exception\Client\MethodNotFoundException;
use Mekras\JsonRpcClient\Exception\Client\ParseErrorException;
use Mekras\JsonRpcClient\Exception\InternalClientException;
use Mekras\JsonRpcClient\Exception\JsonRpcClientException;
use Mekras\JsonRpcClient\Exception\NetworkException;
use Mekras\JsonRpcClient\Exception\Server\InternalErrorException;
use Mekras\JsonRpcClient\Exception\Server\InvalidResponseException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * JSON-RPC Client.
 *
 * @since 1.0
 */
class Client
{
    /**
     * API endpoint URL.
     *
     * @var string
     */
    private $endpoint;

    /**
     * HTTP client.
     *
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * HTTP requests factory.
     *
     * @var RequestFactoryInterface
     */
    private $httpRequestFactory;

    /**
     * HTTP streams factory.
     *
     * @var StreamFactoryInterface
     */
    private $httpStreamFactory;

    /**
     * Create new JSON-RPC client.
     *
     * @param string                  $endpoint           API endpoint URL.
     * @param ClientInterface         $httpClient         HTTP client.
     * @param RequestFactoryInterface $httpRequestFactory HTTP requests factory.
     * @param StreamFactoryInterface  $httpStreamFactory  HTTP streams factory.
     *
     * @since 1.0
     */
    public function __construct(
        string $endpoint,
        ClientInterface $httpClient,
        RequestFactoryInterface $httpRequestFactory,
        StreamFactoryInterface $httpStreamFactory
    ) {
        assert($endpoint !== '', 'API endpoint URL cannot be blank.');
        $this->endpoint = $endpoint;

        $this->httpClient = $httpClient;
        $this->httpRequestFactory = $httpRequestFactory;
        $this->httpStreamFactory = $httpStreamFactory;
    }

    /**
     * Send request to server.
     *
     * @param Request $request
     *
     * @return Response|null
     *
     * @throws JsonRpcClientException
     *
     * @since 1.0
     */
    public function sendRequest(Request $request): ?Response
    {
        $payload = $this->createPayload($request);
        $httpResponse = $this->sendHttpRequest($request, $payload);

        if ($request->getId() === null) {
            return null;
        }

        return $this->convertResponse($httpResponse);
    }

    /**
     * Convert HTTP response to RPC response.
     *
     * @param ResponseInterface $httpResponse
     *
     * @return Response
     *
     * @throws JsonRpcClientException
     */
    private function convertResponse(ResponseInterface $httpResponse): Response
    {
        $contents = (string) $httpResponse->getBody();

        $payload = @json_decode($contents, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidResponseException(
                sprintf(
                    'Server response "%s" is not a valid JSON: %s',
                    substr($contents, 0, 1024),
                    json_last_error_msg()
                )
            );
        }

        if (!is_array($payload)) {
            throw new InvalidResponseException('Server response does not contain valid object.');
        }

        if (!array_key_exists('id', $payload)) {
            throw new InvalidResponseException('Server response does not contain request ID.');
        }

        if (array_key_exists('error', $payload)) {
            $error = $payload['error'];
            if (!is_array($error)) {
                throw new InvalidResponseException('Response "error" field is not an object.');
            }
            if (!array_key_exists('code', $error)) {
                throw new InvalidResponseException(
                    'Response "error" object does not contain "code" field.'
                );
            }
            $this->handleError($error);
        }

        if (!array_key_exists('result', $payload)) {
            throw new InvalidResponseException(
                'Server response contain neither "result" nor "error".'
            );
        }

        return new Response((string) $payload['id'], $payload['result']);
    }

    /**
     * Create RPC payload.
     *
     * @param Request $request
     *
     * @return array<mixed>
     */
    private function createPayload(Request $request): array
    {
        $payload = [
            'jsonrpc' => '2.0',
            'method' => $request->getMethod(),
        ];

        if (count($request->getParams()) > 0) {
            $payload['params'] = $request->getParams();
        }

        if ($request->getId() !== null) {
            $payload['id'] = $request->getId();
        }

        return $payload;
    }

    /**
     * Handle RPC error.
     *
     * @param array<mixed> $error
     *
     * @throws JsonRpcClientException
     */
    private function handleError(array $error): void
    {
        $code = (int) $error['code'];
        $message = (string) ($error['message'] ?? '(server did not provide a description)');

        switch ($code) {
            case Errors::PARSE_ERROR:
                throw new ParseErrorException($message, $code);
            case Errors::INVALID_REQUEST:
                throw new InvalidRequestException($message, $code);
            case Errors::METHOD_NOT_FOUND:
                throw new MethodNotFoundException($message, $code);
            case Errors::INVALID_PARAMS:
                throw new InvalidParamsException($message, $code);
            case Errors::INTERNAL_ERROR:
                throw new InternalErrorException($message, $code);
            default:
                throw new ClientException($message, $code);
        }
    }

    /**
     * Send HTTP request to RPC endpoint.
     *
     * @param Request      $request
     * @param array<mixed> $payload
     *
     * @return ResponseInterface
     *
     * @throws JsonRpcClientException
     */
    private function sendHttpRequest(Request $request, array $payload): ResponseInterface
    {
        $contents = $this->httpStreamFactory->createStream((string) json_encode($payload));
        $httpRequest = $this->httpRequestFactory
            ->createRequest('POST', $this->endpoint)
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Content-Length', (string) $contents->getSize())
            ->withBody($contents);

        try {
            $httpResponse = $this->httpClient->sendRequest($httpRequest);
        } catch (NetworkExceptionInterface $exception) {
            throw new NetworkException(
                sprintf(
                    'Method "%s" cannot be called because of network issues. %s',
                    $request->getMethod(),
                    $exception->getMessage()
                ),
                0,
                $exception
            );
        } catch (ClientExceptionInterface $exception) {
            throw new InternalClientException(
                sprintf(
                    'Method "%s" cannot be called because of RPC client internal error. %s',
                    $request->getMethod(),
                    $exception->getMessage()
                ),
                0,
                $exception
            );
        }

        return $httpResponse;
    }
}
