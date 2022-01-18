<?php

declare(strict_types=1);

namespace Danon\IntervalTree\Interval;

use DateTimeInterface;
use InvalidArgumentException;

use function count;

/**
 * @template TPoint of DateTimeInterface
 * @implements IntervalInterface<TPoint>
 */
final class DateTimeInterval implements IntervalInterface
{
    /**
     * @var TPoint
     */
    private $low;

    /**
     * @var TPoint
     */
    private $high;

    /**
     * DateTimeInterval constructor
     * @param TPoint $low
     * @param TPoint $high
     */
    public function __construct($low, $high)
    {
        if ($low > $high) {
            throw new InvalidArgumentException('Low interval cannot be greater than high');
        }

        $this->low = $low;
        $this->high = $high;
    }

    /**
     * @phpstan-ignore-next-line
     * @psalm-template TPoint of DateTimeInterface
     * @param TPoint[] $interval
     * @return IntervalInterface<TPoint>
     */
    public static function fromArray(array $interval): IntervalInterface
    {
        if (count($interval) !== 2) {
            throw new InvalidArgumentException('Wrong interval array');
        }
        return new self($interval[0], $interval[1]);
    }

    public function getLow(): DateTimeInterface
    {
        return $this->low;
    }

    public function getHigh(): DateTimeInterface
    {
        return $this->high;
    }

    /**
     * @param IntervalInterface<TPoint> $otherInterval
     * @return bool
     */
    public function equalTo(IntervalInterface $otherInterval): bool
    {
        return $this->getLow()->getTimestamp() === $otherInterval->getLow()->getTimestamp() &&
            $this->getHigh()->getTimestamp() === $otherInterval->getHigh()->getTimestamp();
    }

    /**
     * @param IntervalInterface<TPoint> $otherInterval
     * @return bool
     */
    public function lessThan(IntervalInterface $otherInterval): bool
    {
        return $this->getLow()->getTimestamp() < $otherInterval->getLow()->getTimestamp() ||
            (
                $this->getLow()->getTimestamp() === $otherInterval->getLow()->getTimestamp() &&
                $this->getHigh()->getTimestamp() < $otherInterval->getHigh()->getTimestamp()
            );
    }

    /**
     * @param IntervalInterface<TPoint> $otherInterval
     * @return bool
     */
    public function intersect(IntervalInterface $otherInterval): bool
    {
        return !($this->getHigh() < $otherInterval->getLow() || $otherInterval->getHigh() < $this->getLow());
    }

    /**
     * @param IntervalInterface<TPoint> $otherInterval
     * @return IntervalInterface<TPoint>
     */
    public function merge(IntervalInterface $otherInterval): IntervalInterface
    {
        return new DateTimeInterval(
            min($this->getLow(), $otherInterval->getLow()),
            max($this->getHigh(), $otherInterval->getHigh())
        );
    }
}
