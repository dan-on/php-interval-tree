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
     * @var Pair
     */
    private $pair;

    /** @var null|Interval */
    private $max;

    private function __construct()
    {
    }

    public static function withPair(Pair $pair): self
    {
        $self = new self();
        $self->pair = $pair;
        $self->max = $self->pair->getInterval();

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

    public function getPair(): Pair
    {
        return $this->pair;
    }

    public function lessThan(Node $otherNode): bool
    {
        return $this->getPair()->getInterval()->lessThan($otherNode->getPair()->getInterval());
    }

    public function equalTo(Node $otherNode): bool
    {
        $valueEqual = true;
        if ($this->getPair()->getValue() && $otherNode->getPair()->getValue()) {
            $valueEqual = $this->getPair()->getValue() === $otherNode->getPair()->getValue();
        }
        return $this->getPair()->getInterval()->equalTo($otherNode->getPair()->getInterval()) && $valueEqual;
    }

    public function intersect(Node $otherNode): bool
    {
        return $this->getPair()->getInterval()->intersect($otherNode->getPair()->getInterval());
    }

    public function copyPairFrom(Node $otherNode): void
    {
        $this->pair = $otherNode->getPair();
    }

    public function updateMax(): void
    {
        $this->max = $this->getPair()->getInterval()->getMax();

        if ($this->getRight()->max !== null) {
            $this->max = $this->max->merge($this->getRight()->max);
        }
        if ($this->left->max !== null) {
            $this->max = $this->max->merge($this->getLeft()->max);
        }
    }

    // Other_node does not intersect any node of left subtree, if this.left.max < other_node.item.key.low
    public function notIntersectLeftSubtree(Node $searchNode): bool
    {
        //const comparable_less_than = this.item.key.constructor.comparable_less_than;  // static method
        $high = $this->left->max->getHigh() ?? $this->left->max;
        return Interval::comparableLessThan($high, $searchNode->getPair()->getInterval()->getLow());
    }

    // Other_node does not intersect right subtree if other_node.item.key.high < this.right.key.low
    public function notIntersectRightSubtree(Node $searchNode): bool
    {
        //const comparable_less_than = this.item.key.constructor.comparable_less_than;  // static method
        $low = $this->right->max->getLow() ?? $this->right->getPair()->getInterval()->getLow();
        return Interval::comparableLessThan($searchNode->getPair()->getInterval()->getHigh(), $low);
    }
}
