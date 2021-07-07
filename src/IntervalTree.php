<?php
declare(strict_types=1);

namespace Danon\IntervalTree;

use Generator;
use Iterator;

final class IntervalTree
{
    private $root;
    private $nilNode;

    /**
     * Construct new empty instance of IntervalTree
     */
    public function __construct()
    {
        $this->nilNode = Node::nil();
    }

    /**
     * Returns number of items stored in the interval tree
     *
     * @return int
     */
    public function getSize(): int
    {
        $count = 0;
        $this->treeWalk($this->root, function () use (&$count) {
            $count++;
        });
        return $count;
    }

    /**
     * Returns true if tree is empty
     *
     * @return boolean
     */
    public function isEmpty(): bool
    {
        return ($this->root === null || $this->root === $this->nilNode);
    }

    /**
     * Iterator of nodes which keys intersect with given interval
     * If no values stored in the tree, returns array of keys which intersect given interval
     * @param Interval $interval
     * @return Iterator
     */
    public function iterateIntersections(Interval $interval): Iterator
    {
        $searchNode = Node::withItem(new Item($interval));
        yield from $this->treeSearchInterval($this->root, $searchNode);
    }

    /**
     * Check that interval has intersections
     *
     * @param Interval $interval
     * @return boolean
     */
    public function hasIntersection(Interval $interval): bool
    {
        $nodesIterator = $this->iterateIntersections($interval);
        return $nodesIterator->current() !== null;
    }

    /**
     * Count intervals that has intersections
     *
     * @param Interval $interval
     * @return int
     */
    public function countIntersections(Interval $interval): int
    {
        $nodesIterator = $this->iterateIntersections($interval);
        return iterator_count($nodesIterator);
    }

    /**
     * Insert new item into interval tree
     *
     * @param Interval $interval
     * @param mixed $value - value representing any object (optional)
     * @return Node - returns reference to inserted node
     */
    public function insert(Interval $interval, $value = null): Node
    {
        $insertNode = Node::withItem(new Item($interval, $value));
        $insertNode->setLeft($this->nilNode);
        $insertNode->setRight($this->nilNode);
        $insertNode->setParent(null);
        $insertNode->setColor(NodeColor::red());

        $this->treeInsert($insertNode);
        $this->recalculateMax($insertNode);
        return $insertNode;
    }

    /**
     * Returns true if item {key,value} exist in the tree
     *
     * @param Interval $key interval correspondent to keys stored in the tree
     * @param mixed $value value object to be checked
     * @return bool true if item {key, value} exist in the tree, false otherwise
     */
    public function exist(Interval $key, $value): bool
    {
        $searchNode = Node::withItem(new Item($key, $value));
        return (bool)$this->treeSearch($this->root, $searchNode);
    }

    /**
     * @param Interval $interval
     * @param $value
     * @return bool
     */
    public function remove(Interval $interval, $value): bool
    {
        $searchNode = Node::withItem(new Item($interval, $value));
        $deleteNode = $this->treeSearch($this->root, $searchNode);
        if ($deleteNode) {
            $this->treeDelete($deleteNode);
            return true;
        }
        return false;
    }

    private function recalculateMax($node): void
    {
        $nodeCurrent = $node;
        while ($nodeCurrent->getParent() !== null) {
            $nodeCurrent->getParent()->updateMax();
            $nodeCurrent = $nodeCurrent->getParent();
        }
    }

    private function treeInsert(Node $insertNode): void
    {
        $currentNode = $this->root;
        $parentNode = null;

        if ($this->root === null || $this->root === $this->nilNode) {
            $this->root = $insertNode;
        } else {
            while ($currentNode !== $this->nilNode) {
                $parentNode = $currentNode;
                if ($insertNode->lessThan($currentNode)) {
                    $currentNode = $currentNode->getLeft();
                } else {
                    $currentNode = $currentNode->getRight();
                }
            }

            $insertNode->setParent($parentNode);

            if ($insertNode->lessThan($parentNode)) {
                $parentNode->setLeft($insertNode);
            } else {
                $parentNode->setRight($insertNode);
            }
        }

        $this->insertFixup($insertNode);
    }

    // After insertion insert_node may have red-colored parent, and this is a single possible violation
    // Go upwards to the root and re-color until violation will be resolved
    private function insertFixup(Node $insertNode): void
    {
        $currentNode = $insertNode;
        while ($currentNode->getParent() && $currentNode->getParent()->getColor()->isRed()) {
            $grandfather = $currentNode->getParent()->getParent();
            if ($grandfather && $currentNode->getParent() === $grandfather->getLeft()) {
                $uncleNode = $grandfather->getRight();
                if ($uncleNode->getColor()->isRed()) {
                    $currentNode->getParent()->setColor(NodeColor::black());
                    $uncleNode->setColor(NodeColor::black());
                    $grandfather->setColor(NodeColor::red());
                    $currentNode = $currentNode->getParent()->getParent();
                } else {
                    if ($currentNode === $currentNode->getParent()->getRight()) {
                        $currentNode = $currentNode->getParent();
                        $this->rotateLeft($currentNode);
                    }
                    $currentNode->getParent()->setColor(NodeColor::black());
                    $grandfather->setColor(NodeColor::red());
                    $this->rotateRight($currentNode->getParent()->getParent());
                }
            } else {
                $grandfather = $currentNode->getParent()->getParent();
                $uncleNode = $grandfather->getLeft();
                if ($uncleNode->getColor()->isRed()) {
                    $currentNode->getParent()->setColor(NodeColor::black());
                    $uncleNode->setColor(NodeColor::black());
                    $grandfather->setColor(NodeColor::red());
                    $currentNode = $currentNode->getParent()->getParent();
                } else {
                    if ($currentNode === $currentNode->getParent()->getLeft()) {
                        $currentNode = $currentNode->getParent();
                        $this->rotateRight($currentNode);
                    }
                    $currentNode->getParent()->setColor(NodeColor::black());
                    $grandfather->setColor(NodeColor::red());
                    $this->rotateLeft($currentNode->getParent()->getParent());
                }
            }
        }

        $this->root->setColor(NodeColor::black());
    }

    private function treeDelete(Node $deleteNode): void
    {
        // delete_node has less then 2 children
        if ($deleteNode->getLeft() === $this->nilNode || $deleteNode->getRight() === $this->nilNode) {
            $cutNode = $deleteNode;
        } else { // delete_node has 2 children
            $cutNode = $this->treeSuccessor($deleteNode);
        }

        // fix_node if single child of cut_node
        if ($cutNode->getLeft() !== $this->nilNode) {
            $fixNode = $cutNode->getLeft();
        } else {
            $fixNode = $cutNode->getRight();
        }

        $fixNode->setParent($cutNode->getParent());

        if ($cutNode->getParent() === null) {
            $this->root = $fixNode;
        } else {
            if ($cutNode === $cutNode->getParent()->getLeft()) {
                $cutNode->getParent()->setLeft($fixNode);
            } else {
                $cutNode->getParent()->setRight($fixNode);
            }
            $cutNode->getParent()->updateMax(); // update max property of the parent
        }

        $this->recalculateMax($fixNode); // update max property upward from fix_node to root

        // deleteNode becomes cutNode, it means that we cannot hold reference
        // to node in outer structure and we will have to delete by key, additional search need
        if ($cutNode !== $deleteNode) {
            $deleteNode->copyItemFrom($cutNode);
            $deleteNode->updateMax(); // update max property of the cut node at the new place
            $this->recalculateMax($deleteNode); // update max property upward from deleteNode to root
        }

        if ($cutNode->getColor()->isBlack()) {
            $this->deleteFixup($fixNode);
        }
    }

    private function deleteFixup(Node $fixNode): void
    {
        $currentNode = $fixNode;

        while ($currentNode !== $this->root
            && $currentNode->getParent() !== null
            && $currentNode->getColor()->isBlack()) {
            if ($currentNode === $currentNode->getParent()->getLeft()) {
                $brotherNode = $currentNode->getParent()->getRight();
                if ($brotherNode->getColor()->isRed()) {
                    $brotherNode->setColor(NodeColor::black());
                    $currentNode->getParent()->setColor(NodeColor::red());
                    $this->rotateLeft($currentNode->getParent());
                    $brotherNode = $currentNode->getParent()->getRight();
                }

                if ($brotherNode->getLeft()->getColor()->isBlack()) {
                    $brotherNode->setColor(NodeColor::red());
                    $currentNode = $currentNode->getParent();
                } else {
                    if ($brotherNode->getRight()->getColor()->isBlack()) {
                        $brotherNode->setColor(NodeColor::red());
                        $brotherNode->getLeft()->setColor(NodeColor::black());
                        $this->rotateRight($brotherNode);
                        $brotherNode = $currentNode->getParent()->getRight();
                    }
                    $brotherNode->setColor($currentNode->getParent()->getColor());
                    $currentNode->getParent()->setColor(NodeColor::black());
                    $brotherNode->getRight()->setColor(NodeColor::black());
                    $this->rotateLeft($currentNode->getParent());
                    $currentNode = $this->root;
                }
            } else {
                $brotherNode = $currentNode->getParent()->getLeft();
                if ($brotherNode->getColor()->isRed()) {
                    $brotherNode->setColor(NodeColor::black());
                    $currentNode->getParent()->setColor(NodeColor::red());
                    $this->rotateRight($currentNode->getParent());
                    $brotherNode = $currentNode->getParent()->getLeft();
                }
                if ($brotherNode->getRight()->getColor()->isBlack()) {
                    $brotherNode->setColor(NodeColor::red());
                    $currentNode = $currentNode->getParent();
                } else {
                    if ($brotherNode->getLeft()->getColor()->isBlack()) {
                        $brotherNode->setColor(NodeColor::red());
                        $brotherNode->getRight()->setColor(NodeColor::black());
                        $this->rotateLeft($brotherNode);
                        $brotherNode = $currentNode->getParent()->getLeft();
                    }
                    $brotherNode->setColor($currentNode->getParent()->getColor());
                    $currentNode->getParent()->setColor(NodeColor::black());
                    $brotherNode->getLeft()->setColor(NodeColor::black());
                    $this->rotateRight($currentNode->getParent());
                    $currentNode = $this->root;
                }
            }
        }

        $currentNode->setColor(NodeColor::black());
    }

    private function treeSearch(Node $node, Node $searchNode): ?Node
    {
        if ($node === null || $node === $this->nilNode) {
            return null;
        }

        if ($searchNode->equalTo($node)) {
            return $node;
        }

        if ($searchNode->lessThan($node)) {
            return $this->treeSearch($node->getLeft(), $searchNode);
        }

        return $this->treeSearch($node->getRight(), $searchNode);
    }

    // Original search_interval method; container res support push() insertion
    // Search all intervals intersecting given one
    private function treeSearchInterval(Node $node, $searchNode, &$res = []): Generator
    {
        // if ($node->getLeft() !== $this->nilNode && $node->getLeft()->max >= $low) {
        if ($node->getLeft() !== $this->nilNode && !$node->notIntersectLeftSubtree($searchNode)) {
            yield from $this->treeSearchInterval($node->getLeft(), $searchNode, $res);
        }
        // if ($low <= $node->getHigh() && $node->getLow() <= $high) {
        if ($node->intersect($searchNode)) {
            $res[] = $node;
            yield $node;
        }
        // if ($node->getRight() !== $this->nilNode && $node->getLow() <= $high) {
        if ($node->getRight() !== $this->nilNode && !$node->notIntersectRightSubtree($searchNode)) {
            yield from $this->treeSearchInterval($node->getRight(), $searchNode, $res);
        }
    }

    private function localMinimum(Node $node): Node
    {
        $nodeMin = $node;
        while ($nodeMin->getLeft() !== null && $nodeMin->getLeft() !== $this->nilNode) {
            $nodeMin = $nodeMin->getLeft();
        }
        return $nodeMin;
    }

    public function treeSuccessor($node)
    {
        if ($node->getRight() !== $this->nilNode) {
            $nodeSuccessor = $this->localMinimum($node->getRight());
        } else {
            $currentNode = $node;
            $parentNode = $node->getParent();
            while ($parentNode !== null && $parentNode->getRight() === $currentNode) {
                $currentNode = $parentNode;
                $parentNode = $parentNode->getParent();
            }
            $nodeSuccessor = $parentNode;
        }
        return $nodeSuccessor;
    }

    private function rotateLeft(Node $x): void
    {
        $y = $x->getRight();

        $x->setRight($y->getLeft()); // b goes to x.right

        if ($y->getLeft() !== $this->nilNode) {
            $y->getLeft()->setParent($x); // x becomes parent of b
        }
        $y->setParent($x->getParent()); // move parent

        if ($x->getParent() === null) {
            $this->root = $y; // y becomes root
        } elseif ($x === $x->getParent()->getLeft()) {
            $x->getParent()->setLeft($y);
        } else {
            $x->getParent()->setRight($y);
        }
        $y->setLeft($x); // x becomes left child of y
        $x->setParent($y); // and y becomes parent of x

        if ($x !== $this->nilNode) {
            $x->updateMax();
        }

        $y = $x->getParent();
        if ($y !== null && $y !== $this->nilNode) {
            $y->updateMax();
        }
    }

    private function rotateRight(Node $y): void
    {
        $x = $y->getLeft();

        $y->setLeft($x->getRight()); // b goes to y.left

        if ($x->getRight() !== $this->nilNode) {
            $x->getRight()->setParent($y); // y becomes parent of b
        }
        $x->setParent($y->getParent()); // move parent

        if ($y->getParent() === null) { // x becomes root
            $this->root = $x;
        } elseif ($y === $y->getParent()->getLeft()) {
            $y->getParent()->setLeft($x);
        } else {
            $y->getParent()->setRight($x);
        }
        $x->setRight($y); // y becomes right child of x
        $y->setParent($x); // and x becomes parent of y

        if ($y !== $this->nilNode) {
            $y->updateMax();
        }

        $x = $y->getParent();
        if ($x !== null && $x !== $this->nilNode) {
            $y->updateMax();
        }
    }

    private function treeWalk(Node $node, $action): void
    {
        if ($node !== null && $node !== $this->nilNode) {
            $this->treeWalk($node->getLeft(), $action);
            $action($node);
            $this->treeWalk($node->getRight(), $action);
        }
    }
}
