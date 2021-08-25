<?php
declare(strict_types=1);

namespace Danon\IntervalTree\Tests\Interval;

use Danon\IntervalTree\Interval\DateTimeInterval;
use DateTime;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Danon\IntervalTree\Interval\DateTimeInterval
 */
final class DateTimeIntervalTest extends TestCase
{
    public function testConstruct(): void
    {
        $from = new DateTime();
        $to = new DateTime();
        $interval = new DateTimeInterval($from, $to);
        self::assertInstanceOf(DateTimeInterval::class, $interval);
        self::assertSame($from, $interval->getLow());
        self::assertSame($to, $interval->getHigh());

        // Cannot be created when low greater than high
        $this->expectException(InvalidArgumentException::class);
        new DateTimeInterval(new DateTime('now'), new DateTime('-1 day'));
    }

    public function testFromArray(): void
    {
        $from = new DateTime('2021-08-08 00:00:00');
        $to = new DateTime('2021-08-08 00:00:05');
        $interval = DateTimeInterval::fromArray([$from, $to]);
        self::assertSame($from, $interval->getLow());
        self::assertSame($to, $interval->getHigh());

        // Passed more arguments than needed
        $this->expectException(InvalidArgumentException::class);
        DateTimeInterval::fromArray([$to, $to, $to]);
    }

    public function testEqualTo(): void
    {
        $interval = DateTimeInterval::fromArray([
            new DateTime('2021-08-08 00:00:00'),
            new DateTime('2021-08-08 00:00:05'),
        ]);
        $sameInterval = DateTimeInterval::fromArray([
            new DateTime('2021-08-08 00:00:00'),
            new DateTime('2021-08-08 00:00:05'),
        ]);
        $differentInterval = DateTimeInterval::fromArray([
            new DateTime('2021-12-31 00:00:00'),
            new DateTime('2021-12-31 00:00:05'),
        ]);
        self::assertTrue($interval->equalTo($sameInterval));
        self::assertFalse($interval->equalTo($differentInterval));
        self::assertFalse($sameInterval->equalTo($differentInterval));
    }

    public function testLessThan(): void
    {
        $interval = DateTimeInterval::fromArray([
            new DateTime('2021-08-08 00:00:01'),
            new DateTime('2021-08-08 00:00:02'),
        ]);
        $lessInterval = DateTimeInterval::fromArray([
            new DateTime('2021-08-08 00:00:01'),
            new DateTime('2021-08-08 00:00:01'),
        ]);
        $sameInterval = DateTimeInterval::fromArray([
            new DateTime('2021-08-08 00:00:01'),
            new DateTime('2021-08-08 00:00:02'),
        ]);
        $greaterInterval = DateTimeInterval::fromArray([
            new DateTime('2021-08-08 00:00:01'),
            new DateTime('2021-08-08 00:00:03'),
        ]);
        self::assertFalse($interval->lessThan($lessInterval));
        self::assertFalse($interval->lessThan($sameInterval));
        self::assertTrue($interval->lessThan($greaterInterval));
    }

    public function testIntersect(): void
    {
        $interval = DateTimeInterval::fromArray([
            new DateTime('2021-08-08 00:00:05'),
            new DateTime('2021-08-08 00:00:10'),
        ]);
        $sameInterval = DateTimeInterval::fromArray([
            new DateTime('2021-08-08 00:00:05'),
            new DateTime('2021-08-08 00:00:10'),
        ]);
        $intersectingFromLeftInterval = DateTimeInterval::fromArray([
            new DateTime('2021-08-08 00:00:04'),
            new DateTime('2021-08-08 00:00:06'),
        ]);
        $intersectingFromRightInterval = DateTimeInterval::fromArray([
            new DateTime('2021-08-08 00:00:10'),
            new DateTime('2021-08-08 00:00:11'),
        ]);
        $leftPointInterval = DateTimeInterval::fromArray([
            new DateTime('2021-08-08 00:00:05'),
            new DateTime('2021-08-08 00:00:05'),
        ]);
        $rightPointInterval = DateTimeInterval::fromArray([
            new DateTime('2021-08-08 00:00:10'),
            new DateTime('2021-08-08 00:00:10'),
        ]);
        $notIntersectingInterval1 = DateTimeInterval::fromArray([
            new DateTime('2021-08-08 00:00:12'),
            new DateTime('2021-08-08 00:00:14'),
        ]);
        $notIntersectingInterval2 = DateTimeInterval::fromArray([
            new DateTime('2021-08-08 00:00:00'),
            new DateTime('2021-08-08 00:00:04'),
        ]);
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
        $interval1 = DateTimeInterval::fromArray([
            new DateTime('2021-08-08 00:00:00'),
            new DateTime('2021-08-09 00:00:00'),
        ]);
        $interval2 = DateTimeInterval::fromArray([
            new DateTime('2021-08-09 00:00:00'),
            new DateTime('2021-08-11 00:00:00'),
        ]);
        $mergedInterval = $interval1->merge($interval2);

        self::assertEquals(new DateTime('2021-08-08 00:00:00'), $mergedInterval->getLow());
        self::assertEquals(new DateTime('2021-08-11 00:00:00'), $mergedInterval->getHigh());
    }
}
