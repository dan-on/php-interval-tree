<?php

declare(strict_types=1);

namespace Danon\IntervalTree\Interval;

use DateTimeInterface;
use InvalidArgumentException;

use function count;

final class DateTimeInterval implements IntervalInterface
{
    /**
     * @var DateTimeInterface
     */
    private $low;

    /**
     * @var DateTimeInterface
     */
    private $high;

    /**
     * DateTimeInterval constructor
     * @param DateTimeInterface $low
     * @param DateTimeInterface $high
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
     * @param DateTimeInterface[] $interval
     * @return DateTimeInterval
     */
    public static function fromArray($interval): DateTimeInterval
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
     * @param DateTimeInterval $otherInterval
     * @return bool
     */
    public function equalTo($otherInterval): bool
    {
        return $this->getLow()->getTimestamp() === $otherInterval->getLow()->getTimestamp() &&
            $this->getHigh()->getTimestamp() === $otherInterval->getHigh()->getTimestamp();
    }

    /**
     * @param DateTimeInterval $otherInterval
     * @return bool
     */
    public function lessThan($otherInterval): bool
    {
        return $this->getLow()->getTimestamp() < $otherInterval->getLow()->getTimestamp() ||
            (
                $this->getLow()->getTimestamp() === $otherInterval->getLow()->getTimestamp() &&
                $this->getHigh()->getTimestamp() < $otherInterval->getHigh()->getTimestamp()
            );
    }

    /**
     * @param DateTimeInterval $otherInterval
     * @return bool
     */
    public function intersect($otherInterval): bool
    {
        return !($this->getHigh() < $otherInterval->getLow() || $otherInterval->getHigh() < $this->getLow());
    }

    /**
     * @param DateTimeInterval $otherInterval
     * @return DateTimeInterval
     */
    public function merge($otherInterval): DateTimeInterval
    {
        return new DateTimeInterval(
            min($this->getLow(), $otherInterval->getLow()),
            max($this->getHigh(), $otherInterval->getHigh())
        );
    }
}
