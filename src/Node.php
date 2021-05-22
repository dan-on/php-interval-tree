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

    public function __construct($key, $value = null)
    {
        if ($key && is_array($key) && count($key) === 2) {
            $item = new Item(Interval::fromArray($key), $value);
            $this->item = $item;
            $this->max = $this->item->getKey();
        }
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

    public function lessThan(Node $otherNode): bool
    {
        return $this->item->getKey()->lessThan($otherNode->item->getKey());
    }

    public function equalTo(Node $otherNode): bool
    {
        $valueEqual = true;
        if ($this->item->getValue() && $otherNode->item->getValue()) {
            $valueEqual = $this->item->getValue()
                ? $this->item->getValue()->equalTo($otherNode->item->getValue())
                : $this->item->getValue() === $otherNode->item->getValue();
        }
        return $this->item->getKey()->equalTo($otherNode->item->getKey()) && $valueEqual;
    }

    public function intersect(Node $otherNode): bool
    {
        return $this->item->getKey()->intersect($otherNode->item->getKey());
    }

    public function copyData(Node $otherNode)
    {
        $this->item = clone $otherNode->item;
    }

    public function updateMax(): void
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
    public function notIntersectLeftSubtree(Node $searchNode): bool
    {
        //const comparable_less_than = this.item.key.constructor.comparable_less_than;  // static method
        $high = $this->left->max->getHigh() ?? $this->left->max;
        return Interval::comparableLessThan($high, $searchNode->item->getKey()->getLow());
    }

    // Other_node does not intersect right subtree if other_node.item.key.high < this.right.key.low
    public function notIntersectRightSubtree(Node $searchNode): bool
    {
        //const comparable_less_than = this.item.key.constructor.comparable_less_than;  // static method
        $low = $this->right->max->getLow() ?? $this->right->item->getKey()->getLow();
        return Interval::comparableLessThan($searchNode->item->getKey()->getHigh(), $low);
    }
}
