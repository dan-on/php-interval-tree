<?php

namespace Danon\IntervalTree;

class Node
{
    public const COLOR_RED = 0;
    public const COLOR_BLACK = 1;

    /**
     * Reference to left child node
     *
     * @var Node
     */
    private $left;

    /**
     * Reference to right child node
     *
     * @var Node
     */
    private $right;

    /**
     * Reference to parent node
     *
     * @var Node
     */
    private $parent;

    /**
     * Color of node (BLACK or RED)
     *
     * @var int
     */
    public $color;

    /**
     * Key and value
     *
     * @var object
     */
    private $item;

    private $max;

    public function __construct($key = null, $value = null)
    {
        if (is_null($key)) {
            $this->item = new Item($key, $value); // key is supposed to be instance of Interval
        } elseif ($key && is_array($key) && count($key) === 2) {
            $item = new Item(new Interval(min($key), max($key)), $value);
            $this->item = $item;
        }

        $this->max = $this->item->getKey() ? clone $this->item->getKey() : null;
    }

    public function getLeft(): Node
    {
        return $this->left;
    }

    public function setLeft(Node $node): void
    {
        $this->left = $node;
    }

    public function getRight(): Node
    {
        return $this->right;
    }

    public function setRight(Node $node): void
    {
        $this->right = $node;
    }

    public function getParent(): ?Node
    {
        return $this->parent;
    }

    public function setParent(?Node $node): void
    {
        $this->parent = $node;
    }

    public function getValue()
    {
        return $this->item->getValue();
    }

    public function getKey()
    {
        return $this->item->getKey();
    }

    public function isNil()
    {
        return ($this->item->getKey() === null && $this->item->getValue() === null &&
            $this->left === null && $this->right === null && $this->color === Node::COLOR_BLACK);
    }

    public function lessThan($otherNode)
    {
        return $this->item->getKey()->lessThan($otherNode->item->getKey());
    }

    public function equalTo($otherNode)
    {
        $valueEqual = true;
        if ($this->item->getValue() && $otherNode->item->getValue()) {
            $valueEqual = $this->item->getValue()
                ? $this->item->getValue()->equalTo($otherNode->item->getValue())
                : $this->item->getValue() === $otherNode->item->getValue();
        }
        return $this->item->getKey()->equalTo($otherNode->item->getKey()) && $valueEqual;
    }

    public function intersect($otherNode)
    {
        return $this->item->getKey()->intersect($otherNode->item->getKey());
    }

    public function copyData($otherNode)
    {
        $this->item = clone $otherNode->item;
    }

    public function updateMax()
    {
        // use key (Interval) max property instead of key.high
        $this->max = $this->item->getKey() ? $this->item->getKey()->getMax() : null;
        if ($this->right && $this->right->max) {
            $this->max = Interval::comparableMax($this->max, $this->right->max); // static method
        }
        if ($this->left && $this->left->max) {
            $this->max = Interval::comparableMax($this->max, $this->left->max);
        }
    }

    // Other_node does not intersect any node of left subtree, if this.left.max < other_node.item.key.low
    public function notIntersectLeftSubtree($searchNode)
    {
        //const comparable_less_than = this.item.key.constructor.comparable_less_than;  // static method
        $high = $this->left->max->getHigh() !== null ? $this->left->max->getHigh() : $this->left->max;
        return Interval::comparableLessThan($high, $searchNode->item->getKey()->getLow());
    }

    // Other_node does not intersect right subtree if other_node.item.key.high < this.right.key.low
    public function notIntersectRightSubtree($searchNode)
    {
        //const comparable_less_than = this.item.key.constructor.comparable_less_than;  // static method
        $low = $this->right->max->getLow() !== null ? $this->right->max->getLow() : $this->right->item->getKey()->getLow();
        return Interval::comparableLessThan($searchNode->item->getKey()->getHigh(), $low);
    }
}
