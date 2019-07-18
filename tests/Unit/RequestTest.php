<?php

/**
 * JSON-RPC Client.
 *
 * @copyright Михаил Красильников <m.krasilnikov@yandex.ru>
 */

declare(strict_types=1);

namespace Mekras\JsonRpcClient\Tests\Unit;

use Mekras\JsonRpcClient\Request;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Request object.
 *
 * @covers \Mekras\JsonRpcClient\Request
 */
class RequestTest extends TestCase
{
    /**
     * Test adding positional parameters.
     *
     * @throws \Exception
     */
    public function testAddPositionalParameters(): void
    {
        $request1 = new Request('method');
        $request2 = $request1->withParameter('foo');
        $request3 = $request2->withParameter('bar');

        self::assertNotSame($request1, $request2);
        self::assertNotSame($request2, $request3);

        self::assertEquals(['foo', 'bar'], $request3->getParams());
    }

    /**
     * Test construction with all possible arguments.
     *
     * @throws \Exception
     */
    public function testConstructWithAllArguments(): void
    {
        $request = new Request('method', ['foo' => 'bar', 'baz'], 'id');

        self::assertEquals('method', $request->getMethod());
        self::assertEquals(['foo' => 'bar', 0 => 'baz'], $request->getParams());
        self::assertEquals('id', $request->getId());
    }

    /**
     * Test default property values.
     *
     * @throws \Exception
     */
    public function testDefaultValues(): void
    {
        $request = new Request('method');

        self::assertEquals('method', $request->getMethod());
        self::assertEquals([], $request->getParams());
        self::assertNull($request->getId());
    }

    /**
     * Test setting named parameters.
     *
     * @throws \Exception
     */
    public function testSetNamedParameters(): void
    {
        $request1 = new Request('method');
        $request2 = $request1->withNamedParameter('foo', 'FOO');
        $request3 = $request2->withNamedParameter('bar', 'BAR');

        self::assertNotSame($request1, $request2);
        self::assertNotSame($request2, $request3);

        self::assertEquals(['foo' => 'FOO', 'bar' => 'BAR'], $request3->getParams());
    }
}
