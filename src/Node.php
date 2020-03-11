<?php
namespace IntervalTree;

class Node {

    public const COLOR_RED = 0;
    public const COLOR_BLACK = 1;

    /**
     * Reference to left child node
     */
    public $left;

    /**
     * Reference to right child node
     */
    public $right;

    /**
     * Reference to parent node
     */
    public $parent;

    /**
     * Color of node (BLACK or RED)
     */
    public $color;

    public $item;

    public $max;

    public function __construct($key = null, $value = null, $left = null, $right = null, $parent = null, $color = self::COLOR_BLACK) {

        $this->left = $left;
        $this->right = $right;
        $this->parent = $parent;
        $this->color = $color;

        $this->item = (object)compact('key', 'value');   // key is supposed to be instance of Interval

        /* If not, this should by an array of two numbers */
        if ($key && is_array($key) && count($key) === 2) {
            $this->item->key = new Interval(min($key), max($key));
        }

        $this->max = $this->item->key ? clone $this->item->key : null;
    }

    public function isNil() {
        return ($this->item->key === null && $this->item->value === null &&
            $this->left === null && $this->right === null && $this->color === Node::COLOR_BLACK);
    }

    public function lessThan($otherNode) {
        return $this->item->key->lessThan($otherNode->item->key);
    }

    public function equalTo($otherNode) {
        $valueEqual = true;
        if ($this->item->value && $otherNode->item->value) {
            $valueEqual = $this->item->value ? $this->item->value->equalTo($otherNode->item->value) :
                $this->item->value == $otherNode->item->value;
        }
        return $this->item->key->equalTo($otherNode->item->key) && $valueEqual;
    }

    public function intersect($otherNode) {
        return $this->item->key->intersect($otherNode->item->key);
    }

    public function copyData($otherNode) {
        $this->item->key = clone $otherNode->item->key;
        $this->item->value = $otherNode->item->value;
    }

    public function updateMax() {
        // use key (Interval) max property instead of key.high
        $this->max = $this->item->key ? $this->item->key->getMax() : null;
        if ($this->right && $this->right->max) {
            $this->max = Interval::comparableMax($this->max, $this->right->max);  // static method
        }
        if ($this->left && $this->left->max) {
            $this->max = Interval::comparableMax($this->max, $this->left->max);
        }
    }

    // Other_node does not intersect any node of left subtree, if this.left.max < other_node.item.key.low
    public function notIntersectLeftSubtree($searchNode) {
        //const comparable_less_than = this.item.key.constructor.comparable_less_than;  // static method
        $high = $this->left->max->high !== null ? $this->left->max->high : $this->left->max;
        return Interval::comparableLessThan($high, $searchNode->item->key->low);
    }

    // Other_node does not intersect right subtree if other_node.item.key.high < this.right.key.low
    public function notIntersectRightSubtree($searchNode) {
        //const comparable_less_than = this.item.key.constructor.comparable_less_than;  // static method
        $low = $this->right->max->low !== null ? $this->right->max->low : $this->right->item->key->low;
        return Interval::comparableLessThan($searchNode->item->key->high, $low);
    }
}