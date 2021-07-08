<?php
declare(strict_types=1);

namespace Danon\IntervalTree;

use InvalidArgumentException;

final class Interval
{
    /**
     * @var int
     */
    private $low;

    /**
     * @var int
     */
    private $high;

    public function __construct(int $low, int $high)
    {
        if ($low > $high) {
            throw new InvalidArgumentException('Low interval cannot be greater than high');
        }

        $this->low = $low;
        $this->high = $high;
    }

    /**
     * @param int[] $interval
     * @return static
     */
    public static function fromArray(array $interval): self
    {
        if (count($interval) !== 2) {
            throw new InvalidArgumentException('Wrong interval array');
        }
        return new self($interval[0], $interval[1]);
    }

    public function getLow(): int
    {
        return $this->low;
    }

    public function getHigh(): int
    {
        return $this->high;
    }

    public function lessThan(Interval $otherInterval): bool
    {
        return $this->getLow() < $otherInterval->getLow() ||
            ($this->getLow() === $otherInterval->getLow() && $this->getHigh() < $otherInterval->getHigh());
    }

    public function equalTo(Interval $otherInterval): bool
    {
        return $this->getLow() === $otherInterval->getLow() && $this->getHigh() === $otherInterval->getHigh();
    }

    public function intersect(Interval $otherInterval): bool
    {
        return !$this->notIntersect($otherInterval);
    }

    public function notIntersect(Interval $otherInterval): bool
    {
        return ($this->getHigh() < $otherInterval->getLow() || $otherInterval->getHigh() < $this->getLow());
    }

    public function merge(Interval $otherInterval): Interval
    {
        return new Interval(
            min($this->getLow(), $otherInterval->getLow()),
            max($this->getHigh(), $otherInterval->getHigh())
        );
    }

    /**
     * Function returns maximum between two comparable values
     *
     * @param Interval $interval1
     * @param Interval $interval2
     * @return Interval
     */
    public static function comparableMax(Interval $interval1, Interval $interval2): self
    {
        return $interval1->merge($interval2);
    }

    public function getMax(): Interval
    {
        return $this;
    }

    public static function comparableLessThan(int $val1, int $val2): bool
    {
        return $val1 < $val2;
    }
}
