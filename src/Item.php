<?php
declare(strict_types=1);

namespace Danon\IntervalTree;

final class Item
{
    /** @var Interval  */
    private $key;

    /** @var mixed */
    private $value;

    /**
     * Item constructor.
     * @param Interval $key
     * @param mixed $value
     */
    public function __construct(Interval $key, $value = null)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function getKey(): Interval
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
