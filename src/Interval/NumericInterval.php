<?php
declare(strict_types=1);

namespace Danon\IntervalTree\Interval;

use InvalidArgumentException;
use function count;

final class NumericInterval implements IntervalInterface
{
    /**
     * @var int|float
     */
    private $low;

    /**
     * @var int|float
     */
    private $high;

    /**
     * IntegerInterval constructor
     * @param int|float $low
     * @param int|float $high
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
     * @param int[] $interval
     * @return NumericInterval
     */
    public static function fromArray($interval): NumericInterval
    {
        if (count($interval) !== 2) {
            throw new InvalidArgumentException('Wrong interval array');
        }
        return new self($interval[0], $interval[1]);
    }

    /**
     * @return int|float
     */
    public function getLow()
    {
        return $this->low;
    }

    /**
     * @return int|float
     */
    public function getHigh()
    {
        return $this->high;
    }

    /**
     * @param NumericInterval $otherInterval
     * @return bool
     */
    public function equalTo($otherInterval): bool
    {
        return $this->getLow() === $otherInterval->getLow() && $this->getHigh() === $otherInterval->getHigh();
    }

    /**
     * @param NumericInterval $otherInterval
     * @return bool
     */
    public function lessThan($otherInterval): bool
    {
        return $this->getLow() < $otherInterval->getLow() ||
            ($this->getLow() === $otherInterval->getLow() && $this->getHigh() < $otherInterval->getHigh());
    }

    /**
     * @param NumericInterval $otherInterval
     * @return bool
     */
    public function intersect($otherInterval): bool
    {
        return !($this->getHigh() < $otherInterval->getLow() || $otherInterval->getHigh() < $this->getLow());
    }

    /**
     * @param NumericInterval $otherInterval
     * @return NumericInterval
     */
    public function merge($otherInterval): NumericInterval
    {
        return new NumericInterval(
            min($this->getLow(), $otherInterval->getLow()),
            max($this->getHigh(), $otherInterval->getHigh())
        );
    }
}
