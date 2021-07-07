<?php

namespace Danon\IntervalTree;

class Node
{
    /**
     * @var Node
     */
    private $left;

    /**
     * @var Node
     */
    private $right;

    /**
     * @var Node
     */
    private $parent;

    /**
     * @var NodeColor
     */
    public $color;

    /**
     * @var Item
     */
    private $item;

    private $max;

    private function __construct()
    {
    }

    public static function withItem(Item $item): self
    {
        $self = new self();
        $self->item = $item;
        $self->max = $self->item->getKey();

        return $self;
    }

    public static function nil(): self
    {
        $self = new self;
        $self->color = NodeColor::black();
        return $self;
    }

    public function setColor(NodeColor $color): void
    {
        $this->color = $color;
    }

    public function getColor(): NodeColor
    {
        return $this->color;
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

    public function getItem(): Item
    {
        return $this->item;
    }

    public function lessThan(Node $otherNode): bool
    {
        return $this->item->getKey()->lessThan($otherNode->item->getKey());
    }

    public function equalTo(Node $otherNode): bool
    {
        $valueEqual = true;
        if ($this->item->getValue() && $otherNode->item->getValue()) {
            $valueEqual = $this->item->getValue() === $otherNode->item->getValue();
        }
        return $this->item->getKey()->equalTo($otherNode->item->getKey()) && $valueEqual;
    }

    public function intersect(Node $otherNode): bool
    {
        return $this->item->getKey()->intersect($otherNode->item->getKey());
    }

    public function copyItemFrom(Node $otherNode): void
    {
        $this->item = clone $otherNode->item;
    }

    public function updateMax(): void
    {
        // use key (Interval) max property instead of key.high
        $this->max = $this->item->getKey() ? $this->item->getKey()->getMax() : null;

        if ($this->getRight() && $this->getRight()->max) {
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
