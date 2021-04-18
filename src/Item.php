<?php

namespace Danon\IntervalTree;

class Item
{
    public $key;
    public $value;

    public function __construct(?Interval $key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function getKey(): ?Interval
    {
        return $this->key;
    }

    
    public function getValue()
    {
        return $this->value;
    }
}
