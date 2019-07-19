<?php

/**
 * JSON-RPC Client.
 *
 * @copyright Михаил Красильников <m.krasilnikov@yandex.ru>
 */

declare(strict_types=1);

namespace Mekras\JsonRpcClient\Tests\Unit;

use Mekras\JsonRpcClient\Client;
use Mekras\JsonRpcClient\Exception\Client\ClientException;
use Mekras\JsonRpcClient\Exception\Client\InvalidParamsException;
use Mekras\JsonRpcClient\Exception\Client\InvalidRequestException;
use Mekras\JsonRpcClient\Exception\Client\MethodNotFoundException;
use Mekras\JsonRpcClient\Exception\Client\ParseErrorException;
use Mekras\JsonRpcClient\Exception\InternalClientException;
use Mekras\JsonRpcClient\Exception\NetworkException;
use Mekras\JsonRpcClient\Exception\Server\InternalErrorException;
use Mekras\JsonRpcClient\Exception\Server\InvalidResponseException;
use Mekras\JsonRpcClient\Request;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Tests for RPC client.
 *
 * @covers \Mekras\JsonRpcClient\Client
 */
class ClientTest extends TestCase
{
    /**
     * API endpoint for tests.
     */
    private const API_ENDPOINT = 'http://exampple.com/rpc';

    /**
     * Test request ID.
     */
    private const REQUEST_ID = '123456';

    /**
     * Client under test.
     *
     * @var Client
     */
    private $client;

    /**
     * HTTP client.
     *
     * @var ClientInterface|MockObject
     */
    private $httpClient;

    /**
     * HTTP requests factory.
     *
     * @var RequestFactoryInterface|MockObject
     */
    private $httpRequestFactory;

    /**
     * HTTP streams factory.
     *
     * @var StreamFactoryInterface|MockObject
     */
    private $httpStreamFactory;

    /**
     * Provide test data for testHttpRequestFailure.
     *
     * @return array
     */
    public function httpErrorProvider(): array
    {
        return [
            [
                'exception' => $this->createMock(NetworkExceptionInterface::class),
                'expectedException' => NetworkException::class,
                'expectedExceptionMessage' => 'Method "rpc_method" cannot be called because of network issues. '
            ],
            [
                'exception' => $this->createMock(ClientExceptionInterface::class),
                'expectedException' => InternalClientException::class,
                'expectedExceptionMessage' => 'Method "rpc_method" cannot be called because of RPC client internal error. '
            ]
        ];
    }

    /**
     * Provide test data for testServerError.
     *
     * @return array
     */
    public function serverErrorProvider(): array
    {
        return [
            [
                'response' => 'Not a JSON',
                'expectedException' => InvalidResponseException::class,
                'expectedExceptionMessage' => 'Server response "Not a JSON" is not a valid JSON: Syntax error'
            ],
            [
                'response' => 'null',
                'expectedException' => InvalidResponseException::class,
                'expectedExceptionMessage' => 'Server response does not contain valid object.'
            ],
            [
                'response' => '{}',
                'expectedException' => InvalidResponseException::class,
                'expectedExceptionMessage' => 'Server response does not contain request ID.'
            ],
            [
                'response' => '{"id":"' . self::REQUEST_ID . '"}',
                'expectedException' => InvalidResponseException::class,
                'expectedExceptionMessage' => 'Server response contain neither "result" nor "error".'
            ],
            [
                'response' => '{"id":"' . self::REQUEST_ID . '","error":"foo"}',
                'expectedException' => InvalidResponseException::class,
                'expectedExceptionMessage' => 'Response "error" field is not an object.'
            ],
            [
                'response' => '{"id":"' . self::REQUEST_ID . '","error":{}}',
                'expectedException' => InvalidResponseException::class,
                'expectedExceptionMessage' => 'Response "error" object does not contain "code" field.'
            ],
            [
                'response' => '{"id":"' . self::REQUEST_ID . '","error":{"code":-32000,"message":"Error description"}}',
                'expectedException' => ClientException::class,
                'expectedExceptionMessage' => 'Error description'
            ],
            [
                'response' => '{"id":"' . self::REQUEST_ID . '","error":{"code":-32700,"message":"Error description"}}',
                'expectedException' => ParseErrorException::class,
                'expectedExceptionMessage' => 'Error description'
            ],
            [
                'response' => '{"id":"' . self::REQUEST_ID . '","error":{"code":-32600,"message":"Error description"}}',
                'expectedException' => InvalidRequestException::class,
                'expectedExceptionMessage' => 'Error description'
            ],
            [
                'response' => '{"id":"' . self::REQUEST_ID . '","error":{"code":-32601,"message":"Error description"}}',
                'expectedException' => MethodNotFoundException::class,
                'expectedExceptionMessage' => 'Error description'
            ],
            [
                'response' => '{"id":"' . self::REQUEST_ID . '","error":{"code":-32602,"message":"Error description"}}',
                'expectedException' => InvalidParamsException::class,
                'expectedExceptionMessage' => 'Error description'
            ],
            [
                'response' => '{"id":"' . self::REQUEST_ID . '","error":{"code":-32603,"message":"Error description"}}',
                'expectedException' => InternalErrorException::class,
                'expectedExceptionMessage' => 'Error description'
            ]
        ];
    }

    /**
     * Test HTTP errors handling.
     *
     * @param \Throwable $exception
     * @param string     $expectedException
     * @param string     $expectedExceptionMessage
     *
     * @throws \Throwable
     *
     * @dataProvider httpErrorProvider
     */
    public function testHttpRequestFailure(
        \Throwable $exception,
        string $expectedException,
        string $expectedExceptionMessage
    ): void {
        $requestId = self::REQUEST_ID;
        $request = new Request('rpc_method', ['foo' => 'bar'], $requestId);

        $requestStream = $this->createConfiguredMock(StreamInterface::class, ['getSize' => 1024]);

        $this->httpStreamFactory
            ->expects(self::once())
            ->method('createStream')
            ->with('{"jsonrpc":"2.0","method":"rpc_method","params":{"foo":"bar"},"id":"123456"}')
            ->willReturn($requestStream);

        $httpRequest = $this->createMock(RequestInterface::class);

        $this->httpRequestFactory
            ->expects(self::once())
            ->method('createRequest')
            ->with('POST', self::API_ENDPOINT)
            ->willReturn($httpRequest);

        $httpRequest
            ->expects(self::exactly(3))
            ->method('withHeader')
            ->withConsecutive(
                ['Accept', 'application/json'],
                ['Content-Type', 'application/json'],
                ['Content-Length', 1024]
            )
            ->willReturnSelf();

        $httpRequest
            ->expects(self::once())
            ->method('withBody')
            ->with(self::identicalTo($requestStream))
            ->willReturnSelf();

        $this->httpClient
            ->expects(self::once())
            ->method('sendRequest')
            ->with(self::identicalTo($httpRequest))
            ->willThrowException($exception);


        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->client->sendRequest($request);
    }

    /**
     * Test successful RPC call.
     *
     * @throws \Throwable
     */
    public function testSendRpcRequest(): void
    {
        $request = new Request('rpc_method', ['foo' => 'bar'], self::REQUEST_ID);

        $requestStream = $this->createConfiguredMock(StreamInterface::class, ['getSize' => 1024]);

        $this->httpStreamFactory
            ->expects(self::once())
            ->method('createStream')
            ->with('{"jsonrpc":"2.0","method":"rpc_method","params":{"foo":"bar"},"id":"123456"}')
            ->willReturn($requestStream);

        $httpRequest = $this->createMock(RequestInterface::class);

        $this->httpRequestFactory
            ->expects(self::once())
            ->method('createRequest')
            ->with('POST', self::API_ENDPOINT)
            ->willReturn($httpRequest);

        $httpRequest
            ->expects(self::exactly(3))
            ->method('withHeader')
            ->withConsecutive(
                ['Accept', 'application/json'],
                ['Content-Type', 'application/json'],
                ['Content-Length', 1024]
            )
            ->willReturnSelf();

        $httpRequest
            ->expects(self::once())
            ->method('withBody')
            ->with(self::identicalTo($requestStream))
            ->willReturnSelf();

        $responseStream = $this->createConfiguredMock(
            StreamInterface::class,
            ['__toString' => '{"id":"' . self::REQUEST_ID . '","result":{"foo":"bar"}}']
        );

        $httpResponse = $this->createConfiguredMock(
            ResponseInterface::class,
            ['getBody' => $responseStream]
        );

        $this->httpClient
            ->expects(self::once())
            ->method('sendRequest')
            ->with(self::identicalTo($httpRequest))
            ->willReturn($httpResponse);

        $response = $this->client->sendRequest($request);

        self::assertNotNull($response);
        self::assertEquals(self::REQUEST_ID, $response->getId());
        self::assertEquals(['foo' => 'bar'], $response->getResult());
    }

    /**
     * Test server errors handling.
     *
     * @param string $response
     * @param string $expectedException
     * @param string $expectedExceptionMessage
     *
     * @throws \Throwable
     *
     * @dataProvider serverErrorProvider
     */
    public function testServerError(
        string $response,
        string $expectedException,
        string $expectedExceptionMessage
    ): void {
        $requestId = self::REQUEST_ID;
        $request = new Request('rpc_method', ['foo' => 'bar'], $requestId);

        $requestStream = $this->createConfiguredMock(StreamInterface::class, ['getSize' => 1024]);

        $this->httpStreamFactory
            ->expects(self::once())
            ->method('createStream')
            ->with('{"jsonrpc":"2.0","method":"rpc_method","params":{"foo":"bar"},"id":"123456"}')
            ->willReturn($requestStream);

        $httpRequest = $this->createMock(RequestInterface::class);

        $this->httpRequestFactory
            ->expects(self::once())
            ->method('createRequest')
            ->with('POST', self::API_ENDPOINT)
            ->willReturn($httpRequest);

        $httpRequest
            ->expects(self::exactly(3))
            ->method('withHeader')
            ->withConsecutive(
                ['Accept', 'application/json'],
                ['Content-Type', 'application/json'],
                ['Content-Length', 1024]
            )
            ->willReturnSelf();

        $httpRequest
            ->expects(self::once())
            ->method('withBody')
            ->with(self::identicalTo($requestStream))
            ->willReturnSelf();

        $responseStream = $this->createConfiguredMock(
            StreamInterface::class,
            ['__toString' => $response]
        );

        $httpResponse = $this->createConfiguredMock(
            ResponseInterface::class,
            ['getBody' => $responseStream]
        );

        $this->httpClient
            ->expects(self::once())
            ->method('sendRequest')
            ->with(self::identicalTo($httpRequest))
            ->willReturn($httpResponse);


        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->client->sendRequest($request);
    }

    /**
     * Set up test environment.
     *
     * @throws \Throwable
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClient = $this->createMock(ClientInterface::class);
        $this->httpRequestFactory = $this->createMock(RequestFactoryInterface::class);
        $this->httpStreamFactory = $this->createMock(StreamFactoryInterface::class);

        $this->client = new Client(
            self::API_ENDPOINT,
            $this->httpClient,
            $this->httpRequestFactory,
            $this->httpStreamFactory
        );
    }
}
