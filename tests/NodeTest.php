<?php

namespace Danon\IntervalTree\Tests;

use Danon\IntervalTree\Interval\NumericInterval;
use Danon\IntervalTree\Node;
use Danon\IntervalTree\NodeColor;
use Danon\IntervalTree\Pair;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Danon\IntervalTree\Node
 */
class NodeTest extends TestCase
{
    /**
     * @uses \Danon\IntervalTree\Pair
     * @uses \Danon\IntervalTree\Interval\NumericInterval
     */
    public function testWithPair(): void
    {
        $node = Node::withPair(new Pair(NumericInterval::fromArray([1, 5]), 'val'));
        self::assertEquals(1, $node->getPair()->getInterval()->getLow());
        self::assertEquals(5, $node->getPair()->getInterval()->getHigh());
        self::assertEquals('val', $node->getPair()->getValue());
    }

    /**
     * @uses \Danon\IntervalTree\NodeColor
     */
    public function testNil(): void
    {
        $node = Node::nil();
        self::assertTrue($node->getColor()->isBlack());
        self::assertNull($node->getParent());
    }

    /**
     * @uses \Danon\IntervalTree\Pair
     * @uses \Danon\IntervalTree\Interval\NumericInterval
     */
    public function testGetParent(): void
    {
        $node = Node::withPair(new Pair(NumericInterval::fromArray([1, 5])));
        $parentNode = Node::withPair(new Pair(NumericInterval::fromArray([1, 2])));
        $node->setParent($parentNode);
        self::assertSame($node->getParent(), $parentNode);
    }

    /**
     * @uses \Danon\IntervalTree\Pair
     * @uses \Danon\IntervalTree\Interval\NumericInterval
     */
    public function testCopyPairFrom(): void
    {
        $node = Node::withPair(new Pair(NumericInterval::fromArray([1, 5])));
        $otherNode = Node::withPair(new Pair(NumericInterval::fromArray([0, 3])));
        $node->copyPairFrom($otherNode);
        self::assertTrue($node->equalTo($otherNode));
    }

    /**
     * @uses \Danon\IntervalTree\Pair
     * @uses \Danon\IntervalTree\Interval\NumericInterval
     * @uses \Danon\IntervalTree\NodeColor
     */
    public function testGetColor(): void
    {
        $node = Node::withPair(new Pair(NumericInterval::fromArray([1, 5])));
        $node->setColor(NodeColor::red());
        self::assertTrue($node->getColor()->isRed());
        $node->setColor(NodeColor::black());
        self::assertTrue($node->getColor()->isBlack());
    }

    /**
     * @uses \Danon\IntervalTree\Pair
     * @uses \Danon\IntervalTree\Interval\NumericInterval
     */
    public function testEqualTo(): void
    {
        $node = Node::withPair(new Pair(NumericInterval::fromArray([1, 5]), 'foo'));
        $sameNode = Node::withPair(new Pair(NumericInterval::fromArray([1, 5]), 'foo'));
        $otherNode = Node::withPair(new Pair(NumericInterval::fromArray([3, 4]), 'bar'));
        self::assertTrue($node->equalTo($sameNode));
        self::assertFalse($node->equalTo($otherNode));
    }

    /**
     * @uses \Danon\IntervalTree\Pair
     * @uses \Danon\IntervalTree\Interval\NumericInterval
     * @covers \Danon\IntervalTree\Node::getRight
     */
    public function testSetRight(): void
    {
        $node = Node::withPair(new Pair(NumericInterval::fromArray([1, 5])));
        $rightNode = Node::withPair(new Pair(NumericInterval::fromArray([1, 6])));
        $node->setRight($rightNode);
        self::assertSame($node->getRight(), $rightNode);
    }

    /**
     * @uses \Danon\IntervalTree\Pair
     * @uses \Danon\IntervalTree\Interval\NumericInterval
     * @covers \Danon\IntervalTree\Node::getLeft
     */
    public function testSetLeft(): void
    {
        $node = Node::withPair(new Pair(NumericInterval::fromArray([1, 5])));
        $leftNode = Node::withPair(new Pair(NumericInterval::fromArray([1, 6])));
        $node->setLeft($leftNode);
        self::assertSame($node->getLeft(), $leftNode);
    }
}
