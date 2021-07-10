<?php
declare(strict_types=1);

namespace Danon\IntervalTree;

final class Pair
{
    /** @var Interval  */
    private $interval;

    /** @var mixed */
    private $value;

    /**
     * @param Interval $interval
     * @param mixed $value
     */
    public function __construct(Interval $interval, $value = null)
    {
        $this->interval = $interval;
        $this->value = $value;
    }

    public function getInterval(): Interval
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
