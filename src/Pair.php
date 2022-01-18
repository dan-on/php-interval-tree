<?php

declare(strict_types=1);

namespace Danon\IntervalTree;

use Danon\IntervalTree\Interval\IntervalInterface;

/**
 * @template TPoint
 * @template TValue
 */
final class Pair
{
    /** @var IntervalInterface<TPoint> */
    private $interval;

    /** @var TValue */
    private $value;

    /**
     * @param IntervalInterface<TPoint> $interval
     * @param TValue $value
     */
    public function __construct(IntervalInterface $interval, $value = null)
    {
        $this->interval = $interval;
        $this->value = $value;
    }

    /**
     * @return IntervalInterface<TPoint>
     */
    public function getInterval(): IntervalInterface
    {
        return $this->interval;
    }

    /**
     * @return TValue
     */
    public function getValue()
    {
        return $this->value;
    }
}
