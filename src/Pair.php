<?php
declare(strict_types=1);

namespace Danon\IntervalTree;

use Danon\IntervalTree\Interval\IntervalInterface;

final class Pair
{
    /** @var IntervalInterface */
    private $interval;

    /** @var mixed */
    private $value;

    /**
     * @param IntervalInterface $interval
     * @param mixed $value
     */
    public function __construct(IntervalInterface $interval, $value = null)
    {
        $this->interval = $interval;
        $this->value = $value;
    }

    public function getInterval(): IntervalInterface
    {
        return $this->interval;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
