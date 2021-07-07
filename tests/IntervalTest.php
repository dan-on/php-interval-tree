<?php
declare(strict_types=1);

namespace Danon\IntervalTree\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Danon\IntervalTree\Interval;

final class IntervalTest extends TestCase
{
    public function testCanBeCreatedFromCorrectInterval(): void
    {
        self::assertInstanceOf(
            Interval::class,
            new Interval(1, 2)
        );
    }

    public function testCannotBeCreatedFromIncorrectInterval(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Interval(2, 1);
    }
}
