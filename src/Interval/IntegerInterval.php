<?php
declare(strict_types=1);

namespace Danon\IntervalTree\Interval;

use InvalidArgumentException;
use function count;

final class IntegerInterval implements IntervalInterface
{
    /**
     * @var int
     */
    private $low;

    /**
     * @var int
     */
    private $high;

    /**
     * IntegerInterval constructor
     * @param int $low
     * @param int $high
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
     * @return IntegerInterval
     */
    public static function fromArray($interval): IntegerInterval
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

    /**
     * @param IntegerInterval $otherInterval
     * @return bool
     */
    public function equalTo($otherInterval): bool
    {
        return $this->getLow() === $otherInterval->getLow() && $this->getHigh() === $otherInterval->getHigh();
    }

    /**
     * @param IntegerInterval $otherInterval
     * @return bool
     */
    public function lessThan($otherInterval): bool
    {
        return $this->getLow() < $otherInterval->getLow() ||
            ($this->getLow() === $otherInterval->getLow() && $this->getHigh() < $otherInterval->getHigh());
    }

    /**
     * @param IntegerInterval $otherInterval
     * @return bool
     */
    public function intersect($otherInterval): bool
    {
        return !($this->getHigh() < $otherInterval->getLow() || $otherInterval->getHigh() < $this->getLow());
    }

    /**
     * @param IntegerInterval $otherInterval
     * @return IntegerInterval
     */
    public function merge($otherInterval): IntegerInterval
    {
        return new IntegerInterval(
            min($this->getLow(), $otherInterval->getLow()),
            max($this->getHigh(), $otherInterval->getHigh())
        );
    }
}
