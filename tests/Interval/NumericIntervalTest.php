<?php
declare(strict_types=1);

namespace Danon\IntervalTree\Tests\Interval;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Danon\IntervalTree\Interval\NumericInterval;

/**
 * @covers \Danon\IntervalTree\Interval\NumericInterval
 */
final class NumericIntervalTest extends TestCase
{
    public function testConstruct(): void
    {
        $interval = new NumericInterval(1, 2);
        self::assertInstanceOf(NumericInterval::class, $interval);
        self::assertSame(1, $interval->getLow());
        self::assertSame(2, $interval->getHigh());

        // Cannot be created when low greater than high
        $this->expectException(InvalidArgumentException::class);
        new NumericInterval(2, 1);
    }

    public function testFromArray(): void
    {
        $interval = NumericInterval::fromArray([20, 25]);
        self::assertSame(20, $interval->getLow());
        self::assertSame(25, $interval->getHigh());

        // Passed more arguments than needed
        $this->expectException(InvalidArgumentException::class);
        NumericInterval::fromArray([1, 2, 3]);
    }

    public function testEqualTo(): void
    {
        $interval = NumericInterval::fromArray([1, 2]);
        $sameInterval = NumericInterval::fromArray([1, 2]);
        $differentInterval = NumericInterval::fromArray([1, 3]);
        self::assertTrue($interval->equalTo($sameInterval));
        self::assertFalse($interval->equalTo($differentInterval));
        self::assertFalse($sameInterval->equalTo($differentInterval));
    }

    public function testLessThan(): void
    {
        $interval = NumericInterval::fromArray([1, 2]);
        $lessInterval = NumericInterval::fromArray([1, 1]);
        $sameInterval = NumericInterval::fromArray([1, 2]);
        $greaterInterval = NumericInterval::fromArray([1, 3]);
        self::assertFalse($interval->lessThan($lessInterval));
        self::assertFalse($interval->lessThan($sameInterval));
        self::assertTrue($interval->lessThan($greaterInterval));
    }

    public function testIntersect(): void
    {
        $interval = NumericInterval::fromArray([5, 10]);
        $sameInterval = NumericInterval::fromArray([5, 10]);
        $intersectingFromLeftInterval = NumericInterval::fromArray([4, 6]);
        $intersectingFromRightInterval = NumericInterval::fromArray([10, 11]);
        $leftPointInterval = NumericInterval::fromArray([5, 5]);
        $rightPointInterval = NumericInterval::fromArray([10, 10]);
        $notIntersectingInterval1 = NumericInterval::fromArray([12, 14]);
        $notIntersectingInterval2 = NumericInterval::fromArray([0, 4]);
        self::assertTrue($interval->intersect($sameInterval));
        self::assertTrue($interval->intersect($intersectingFromLeftInterval));
        self::assertTrue($interval->intersect($intersectingFromRightInterval));
        self::assertTrue($interval->intersect($leftPointInterval));
        self::assertTrue($interval->intersect($rightPointInterval));
        self::assertFalse($interval->intersect($notIntersectingInterval1));
        self::assertFalse($interval->intersect($notIntersectingInterval2));
    }

    public function testMerge(): void
    {
        $interval1 = NumericInterval::fromArray([1, 3]);
        $interval2 = NumericInterval::fromArray([3, 5]);
        $mergedInterval = $interval1->merge($interval2);
        self::assertSame(1, $mergedInterval->getLow());
        self::assertSame(5, $mergedInterval->getHigh());
    }
}
