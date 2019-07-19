<?php

/**
 * JSON-RPC Client.
 *
 * @copyright Михаил Красильников <m.krasilnikov@yandex.ru>
 */

declare(strict_types=1);

namespace Mekras\JsonRpcClient\Tests\Unit\IdGenerator;

use Mekras\JsonRpcClient\IdGenerator\SequentialIdGenerator;
use PHPUnit\Framework\TestCase;

/**
 * Tests for sequential ID generator.
 *
 * @covers \Mekras\JsonRpcClient\IdGenerator\SequentialIdGenerator
 */
class SequentialIdGeneratorTest extends TestCase
{
    /**
     * Test custom sequence.
     *
     * @throws \Throwable
     */
    public function testCustomSequence(): void
    {
        $generator = new SequentialIdGenerator();

        self::assertEquals('1', $generator->generateId());
        self::assertEquals('2', $generator->generateId());
        self::assertEquals('3', $generator->generateId());
    }

    /**
     * Test default sequence.
     *
     * @throws \Throwable
     */
    public function testDefaultSequence(): void
    {
        $generator = new SequentialIdGenerator(10);

        self::assertEquals('10', $generator->generateId());
        self::assertEquals('11', $generator->generateId());
        self::assertEquals('12', $generator->generateId());
    }
}
