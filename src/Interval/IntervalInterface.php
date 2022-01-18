<?php

declare(strict_types=1);

namespace Danon\IntervalTree\Interval;

/**
 * @template TPoint
 */
interface IntervalInterface
{
    /**
     * @param TPoint $low
     * @param TPoint $high
     */
    public function __construct($low, $high);

    /**
     * @phpstan-ignore-next-line
     * @psalm-template TPoint
     * @param TPoint[] $interval
     * @return IntervalInterface<TPoint>
     */
    public static function fromArray(array $interval): IntervalInterface;

    /**
     * @return TPoint
     */
    public function getLow();

    /**
     * @return TPoint
     */
    public function getHigh();

    /**
     * @param IntervalInterface<TPoint> $otherInterval
     * @return bool
     */
    public function equalTo(IntervalInterface $otherInterval): bool;

    /**
     * @param IntervalInterface<TPoint> $otherInterval
     * @return bool
     */
    public function lessThan(IntervalInterface $otherInterval): bool;

    /**
     * @param IntervalInterface<TPoint> $otherInterval
     * @return bool
     */
    public function intersect(IntervalInterface $otherInterval): bool;

    /**
     * @param IntervalInterface<TPoint> $otherInterval
     * @return IntervalInterface<TPoint>
     */
    public function merge(IntervalInterface $otherInterval): IntervalInterface;
}
