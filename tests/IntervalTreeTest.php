<?php

declare(strict_types=1);

namespace Danon\IntervalTree\Tests;

use Danon\IntervalTree\Interval\NumericInterval;
use Danon\IntervalTree\IntervalTree;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Danon\IntervalTree\IntervalTree
 */
final class IntervalTreeTest extends TestCase
{
    private const TREE_INTERVALS = [
        [7, 8], [1, 4], [2, 3], [7, 12], [1, 1], [3, 4], [7, 7], [0, 2], [0, 2], [0, 3], [9, 12]
    ];

    /** @var IntervalTree  */
    private $tree;

    public function setUp(): void
    {
        $this->tree = new IntervalTree();
        foreach (self::TREE_INTERVALS as $interval) {
            $value = implode('-', $interval);
            $this->tree->insert(
                NumericInterval::fromArray($interval),
                $value
            );
        }
        parent::setUp();
    }

    /**
     * @uses \Danon\IntervalTree\Interval\NumericInterval
     * @uses \Danon\IntervalTree\Node
     * @uses \Danon\IntervalTree\NodeColor
     * @uses \Danon\IntervalTree\Pair
     */
    public function testFindIntersections(): void
    {
        $checkInterval = [2, 3];
        $overlappingIntervals = [[0, 2], [0, 2], [0, 3], [1, 4], [2, 3], [3, 4]];
        $intersections = $this->tree->findIntersections(NumericInterval::fromArray($checkInterval));
        foreach ($intersections as $index => $node) {
            $overlappingInterval = NumericInterval::fromArray($overlappingIntervals[$index]);
            $overlappingValue = implode('-', $overlappingIntervals[$index]);
            self::assertTrue($overlappingInterval->equalTo(NumericInterval::fromArray([
                $node->getPair()->getInterval()->getLow(),
                $node->getPair()->getInterval()->getHigh(),
            ])));
            self::assertEquals($overlappingValue, $node->getPair()->getValue());
        }
    }

    /**
     * @uses \Danon\IntervalTree\Interval\NumericInterval
     * @uses \Danon\IntervalTree\Node
     * @uses \Danon\IntervalTree\NodeColor
     * @uses \Danon\IntervalTree\Pair
     */
    public function testFindAnyIntersection(): void
    {
        self::assertTrue($this->tree->hasIntersection(NumericInterval::fromArray([2, 3])));
        self::assertTrue($this->tree->hasIntersection(NumericInterval::fromArray([0, 1])));
        self::assertTrue($this->tree->hasIntersection(NumericInterval::fromArray([0, 12])));
        self::assertTrue($this->tree->hasIntersection(NumericInterval::fromArray([0, 0])));
        self::assertTrue($this->tree->hasIntersection(NumericInterval::fromArray([0, 99])));
        self::assertTrue($this->tree->hasIntersection(NumericInterval::fromArray([5, 7])));
        self::assertTrue($this->tree->hasIntersection(NumericInterval::fromArray([6, 7])));
        self::assertFalse($this->tree->hasIntersection(NumericInterval::fromArray([13, 14])));
        self::assertFalse($this->tree->hasIntersection(NumericInterval::fromArray([5, 5])));
        self::assertFalse($this->tree->hasIntersection(NumericInterval::fromArray([5, 6])));
        self::assertFalse($this->tree->hasIntersection(NumericInterval::fromArray([6, 6])));
    }

    /**
     * @uses \Danon\IntervalTree\Interval\NumericInterval
     * @uses \Danon\IntervalTree\Node
     * @uses \Danon\IntervalTree\NodeColor
     * @uses \Danon\IntervalTree\Pair
     */
    public function testRemove(): void
    {
        $initialSize = $this->tree->getSize();
        self::assertEquals(count(self::TREE_INTERVALS), $initialSize);
        self::assertTrue($this->tree->remove(NumericInterval::fromArray([7, 8]), '7-8'));
        self::assertEquals($this->tree->getSize(), --$initialSize);
        self::assertFalse($this->tree->remove(NumericInterval::fromArray([1, 4]), '1-3'));
        self::assertEquals($this->tree->getSize(), $initialSize);
        self::assertTrue($this->tree->remove(NumericInterval::fromArray([1, 4]), '1-4'));
        self::assertEquals($this->tree->getSize(), --$initialSize);
        self::assertTrue($this->tree->remove(NumericInterval::fromArray([1, 1]), '1-1'));
        self::assertEquals($this->tree->getSize(), --$initialSize);
        self::assertTrue($this->tree->remove(NumericInterval::fromArray([0, 2]), '0-2'));
        self::assertEquals($this->tree->getSize(), --$initialSize);
        self::assertFalse($this->tree->remove(NumericInterval::fromArray([0, 0]), '0-0'));
        self::assertEquals($this->tree->getSize(), $initialSize);
        self::assertTrue($this->tree->remove(NumericInterval::fromArray([7, 12]), '7-12'));
        self::assertEquals($this->tree->getSize(), --$initialSize);
        self::assertFalse($this->tree->remove(NumericInterval::fromArray([7, 12]), '7-90'));
        self::assertEquals($this->tree->getSize(), $initialSize);
        self::assertFalse($this->tree->remove(NumericInterval::fromArray([7, 12]), '7-12'));
        self::assertEquals($this->tree->getSize(), $initialSize);
    }

    /**
     * @uses \Danon\IntervalTree\Interval\NumericInterval
     * @uses \Danon\IntervalTree\Node
     * @uses \Danon\IntervalTree\NodeColor
     * @uses \Danon\IntervalTree\Pair
     */
    public function testIsEmpty(): void
    {
        self::assertTrue((new IntervalTree())->isEmpty());
        self::assertFalse($this->tree->isEmpty());
    }
}
