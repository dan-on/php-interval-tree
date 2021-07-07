<?php
declare(strict_types=1);

namespace Danon\IntervalTree\Tests;

use Danon\IntervalTree\Interval;
use Danon\IntervalTree\IntervalTree;
use PHPUnit\Framework\TestCase;

final class IntervalTreeTest extends TestCase
{
    private $tree;
    private $intervals = [[7, 8], [1, 4], [2, 3], [7, 12], [1, 1], [3, 4], [7, 7], [0, 2], [0, 2], [0, 3], [9, 12]];

    public function setUp(): void
    {
        $this->tree = new IntervalTree();
        foreach ($this->intervals as $interval) {
            $value = implode('-', $interval);
            $this->tree->insert(
                Interval::fromArray($interval),
                $value
            );
        }
        parent::setUp();
    }

    public function testFindAllIntersections(): void
    {
        $checkInterval = [2, 3];
        $overlappingIntervals = [[0, 2], [0, 2], [0, 3], [1, 4], [2, 3], [3, 4]];
        $intersections = $this->tree->iterateIntersections(Interval::fromArray($checkInterval));
        $index = 0;
        foreach ($intersections as $node) {
            $overlappingInterval = Interval::fromArray($overlappingIntervals[$index]);
            $overlappingValue = implode('-', $overlappingIntervals[$index]);
            self::assertTrue($overlappingInterval->equalTo($node->getItem()->getKey()));
            self::assertEquals($overlappingValue, $node->getItem()->getValue());
            $index++;
        }
    }

    public function testFindAnyIntersection(): void
    {
        self::assertTrue($this->tree->hasIntersection(Interval::fromArray([2, 3])));
        self::assertTrue($this->tree->hasIntersection(Interval::fromArray([0, 1])));
        self::assertTrue($this->tree->hasIntersection(Interval::fromArray([0, 12])));
        self::assertTrue($this->tree->hasIntersection(Interval::fromArray([0, 0])));
        self::assertTrue($this->tree->hasIntersection(Interval::fromArray([0, 99])));
        self::assertTrue($this->tree->hasIntersection(Interval::fromArray([5, 7])));
        self::assertTrue($this->tree->hasIntersection(Interval::fromArray([6, 7])));
        self::assertFalse($this->tree->hasIntersection(Interval::fromArray([13, 14])));
        self::assertFalse($this->tree->hasIntersection(Interval::fromArray([5, 5])));
        self::assertFalse($this->tree->hasIntersection(Interval::fromArray([5, 6])));
        self::assertFalse($this->tree->hasIntersection(Interval::fromArray([6, 6])));
    }

    public function testRemove(): void
    {
        $initialSize = $this->tree->getSize();
        $this->tree->remove(Interval::fromArray([7, 8]), '7-8');
        self::assertEquals($this->tree->getSize(), $initialSize - 1);
        $this->tree->remove(Interval::fromArray([1, 4]), '1-3');
        self::assertEquals($this->tree->getSize(), $initialSize - 1);
        $this->tree->remove(Interval::fromArray([1, 4]), '1-4');
        self::assertEquals($this->tree->getSize(), $initialSize - 2);
    }
}
