<?php

namespace Danon\IntervalTree;

use Danon\IntervalTree\Interval\IntervalInterface;

/**
 * @template TPoint
 * @template TValue
 */
final class Node
{
    /**
     * @var Node<TPoint, TValue>
     */
    private $left;

    /**
     * @var Node<TPoint, TValue>
     */
    private $right;

    /**
     * @var Node<TPoint, TValue>
     */
    private $parent;

    /**
     * @var NodeColor
     */
    private $color;

    /**
     * @var Pair<TPoint, TValue>
     */
    private $pair;

    /**
     * @var null|IntervalInterface<TPoint>
     */
    private $max;

    private function __construct()
    {
    }

    /**
     * @phpstan-ignore-next-line
     * @psalm-template TPoint
     * @psalm-template TValue
     * @param Pair<TPoint, TValue> $pair
     * @return static
     */
    public static function withPair(Pair $pair): self
    {
        $self = new self();
        $self->pair = $pair;
        $self->max = $self->pair->getInterval();

        return $self;
    }

    /**
     * @return Node<TPoint, TValue>
     */
    public static function nil(): self
    {
        $self = new self();
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

    /**
     * @return Node<TPoint, TValue>
     */
    public function getLeft(): Node
    {
        return $this->left;
    }

    /**
     * @param Node<TPoint, TValue> $node
     * @return void
     */
    public function setLeft(Node $node): void
    {
        $this->left = $node;
    }

    /**
     * @return Node<TPoint, TValue>
     */
    public function getRight(): Node
    {
        return $this->right;
    }

    /**
     * @param Node<TPoint, TValue> $node
     * @return void
     */
    public function setRight(Node $node): void
    {
        $this->right = $node;
    }

    /**
     * @return Node<TPoint, TValue>|null
     */
    public function getParent(): ?Node
    {
        return $this->parent;
    }

    /**
     * @param Node<TPoint, TValue>|null $node
     * @return void
     */
    public function setParent(?Node $node): void
    {
        $this->parent = $node;
    }

    /**
     * @return Pair<TPoint, TValue>
     */
    public function getPair(): Pair
    {
        return $this->pair;
    }

    /**
     * @param Node<TPoint, TValue> $otherNode
     * @return bool
     */
    public function lessThan(Node $otherNode): bool
    {
        return $this->getPair()->getInterval()->lessThan($otherNode->getPair()->getInterval());
    }

    /**
     * @param Node<TPoint, TValue> $otherNode
     * @return bool
     */
    public function equalTo(Node $otherNode): bool
    {
        $valueEqual = true;
        if ($this->getPair()->getValue() && $otherNode->getPair()->getValue()) {
            $valueEqual = $this->getPair()->getValue() === $otherNode->getPair()->getValue();
        }
        return $this->getPair()->getInterval()->equalTo($otherNode->getPair()->getInterval()) && $valueEqual;
    }

    /**
     * @param Node<TPoint, TValue> $otherNode
     * @return bool
     */
    public function intersect(Node $otherNode): bool
    {
        return $this->getPair()->getInterval()->intersect($otherNode->getPair()->getInterval());
    }

    /**
     * @param Node<TPoint, TValue> $otherNode
     * @return void
     */
    public function copyPairFrom(Node $otherNode): void
    {
        $this->pair = $otherNode->getPair();
    }

    /**
     * @return void
     */
    public function updateMax(): void
    {
        $this->max = $this->getPair()->getInterval();
        if ($this->getRight()->max !== null) {
            $this->max = $this->max->merge($this->getRight()->max);
        }
        if ($this->getLeft()->max !== null) {
            $this->max = $this->max->merge($this->getLeft()->max);
        }
    }

    /**
     * @param Node<TPoint, TValue> $searchNode
     * @return bool
     */
    public function notIntersectLeftSubtree(Node $searchNode): bool
    {
        $high = $this->getLeft()->max->getHigh() ?? $this->getLeft()->getPair()->getInterval()->getHigh();
        return $high < $searchNode->getPair()->getInterval()->getLow();
    }

    /**
     * @param Node<TPoint, TValue> $searchNode
     * @return bool
     */
    public function notIntersectRightSubtree(Node $searchNode): bool
    {
        $low = $this->getRight()->max->getLow() ?? $this->getRight()->getPair()->getInterval()->getLow();
        return $searchNode->getPair()->getInterval()->getHigh() < $low;
    }
}
