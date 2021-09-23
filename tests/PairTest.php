<?php

namespace Danon\IntervalTree\Tests;

use Danon\IntervalTree\Interval\NumericInterval;
use Danon\IntervalTree\Pair;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Danon\IntervalTree\Pair
 */
final class PairTest extends TestCase
{
    private const EXAMPLE_INTERVAL = [1, 5];
    private const EXAMPLE_VALUE = '1_5';

    /**
     * @var Pair
     */
    protected $pair;

    public function setUp(): void
    {
        $this->pair = new Pair(
            NumericInterval::fromArray(self::EXAMPLE_INTERVAL),
            self::EXAMPLE_VALUE
        );

        parent::setUp();
    }

    /**
     * @uses \Danon\IntervalTree\Interval\NumericInterval
     */
    public function testGetInterval(): void
    {
        self::assertTrue(
            $this->pair->getInterval()->equalTo(
                NumericInterval::fromArray(self::EXAMPLE_INTERVAL)
            )
        );
    }

    /**
     * @uses \Danon\IntervalTree\Interval\NumericInterval
     */
    public function testGetValue(): void
    {
        self::assertSame(self::EXAMPLE_VALUE, $this->pair->getValue());
    }
}
