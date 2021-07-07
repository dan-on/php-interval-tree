<?php
declare(strict_types=1);

namespace Danon\IntervalTree;

final class Item
{
    private $key;
    private $value;

    public function __construct(Interval $key, $value = null)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function getKey(): Interval
    {
        return $this->key;
    }

    public function getValue()
    {
        return $this->value;
    }
}
