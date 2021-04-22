<?php

namespace Danon\IntervalTree;

use Iterator;

class IntervalTree
{
    public $root;
    public $nilNode;

    /**
     * Construct new empty instance of IntervalTree
     */
    public function __construct()
    {
        $this->nilNode = new Node();
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
     * @param array $interval
     * @return Iterator
     */
    public function iterateIntersections(array $interval): Iterator
    {
        $searchNode = new Node($interval);
        yield from $this->treeSearchInterval($this->root, $searchNode);
    }

    /**
     * Check that interval has intersections
     *
     * @param array $interval
     * @return boolean
     */
    public function hasIntersection(array $interval): bool
    {
        $nodesIterator = $this->iterateIntersections($interval);
        return $nodesIterator->current() !== null;
    }

    /**
     * Count intervals that has intersections
     *
     * @param array $interval
     * @return int
     */
    public function countIntersections($interval): int
    {
        $nodesIterator = $this->iterateIntersections($interval);
        return iterator_count($nodesIterator);
    }

    /**
     * Insert new item into interval tree
     *
     * @param array $key - array of two numbers [low, high]
     * @param mixed $value - value representing any object (optional)
     * @return Node - returns reference to inserted node
     */
    public function insert(array $key, $value = null)
    {
        if ($key === null) {
            return;
        }

        if ($value === null) {
            $value = $key;
        }

        $insertNode = new Node($key, $value);
        $insertNode->setLeft($this->nilNode);
        $insertNode->setRight($this->nilNode);
        $insertNode->setParent(null);
        $insertNode->color = Node::COLOR_RED;
        
        $this->treeInsert($insertNode);
        $this->recalcMax($insertNode);
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
        $searchNode = new Node($key, $value);
        return (bool)$this->treeSearch($this->root, $searchNode);
    }

    /**
     * Remove entry {key, value} from the tree
     * @param key - interval correspondent to keys stored in the tree
     * @param value - - value object
     * @return bool - true if item {key, value} deleted, false if not found
     */
    public function remove($key, $value): bool
    {
        $searchNode = new Node($key, $value);
        $deleteNode = $this->treeSearch($this->root, $searchNode);
        if ($deleteNode) {
            $this->treeDelete($deleteNode);
        }
        return true;
    }

    public function recalcMax($node)
    {
        $nodeCurrent = $node;
        while ($nodeCurrent->getParent() !== null) {
            $nodeCurrent->getParent()->updateMax();
            $nodeCurrent = $nodeCurrent->getParent();
        }
    }

    public function treeInsert($insertNode)
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
    public function insertFixup($insertNode)
    {
        $currentNode = $insertNode;
        while ($currentNode !== $this->root && $currentNode->getParent()->color === Node::COLOR_RED) {
            if ($currentNode->getParent() === $currentNode->getParent()->getParent()->getLeft()) { // parent is left child of grandfather
                $uncleNode = $currentNode->getParent()->getParent()->getRight(); // right brother of parent
                if ($uncleNode->color === Node::COLOR_RED) { // Case 1. Uncle is red
                    // re-color father and uncle into black
                    $currentNode->getParent()->color = Node::COLOR_BLACK;
                    $uncleNode->color = Node::COLOR_BLACK;
                    $currentNode->getParent()->getParent()->color = Node::COLOR_RED;
                    $currentNode = $currentNode->getParent()->getParent();
                } else { // Case 2 & 3. Uncle is black
                    if ($currentNode === $currentNode->getParent()->getRight()) { // Case 2. Current if right child
                        // This case is transformed into Case 3.
                        $currentNode = $currentNode->getParent();
                        $this->rotateLeft($currentNode);
                    }
                    $currentNode->getParent()->color = Node::COLOR_BLACK; // Case 3. Current is left child.
                    // Re-color father and grandfather, rotate grandfather right
                    $currentNode->getParent()->getParent()->color = Node::COLOR_RED;
                    $this->rotateRight($currentNode->getParent()->getParent());
                }
            } else { // parent is right child of grandfather
                $uncleNode = $currentNode->getParent()->getParent()->getLeft(); // left brother of parent
                if ($uncleNode->color === Node::COLOR_RED) { // Case 4. Uncle is red
                    // re-color father and uncle into black
                    $currentNode->getParent()->color = Node::COLOR_BLACK;
                    $uncleNode->color = Node::COLOR_BLACK;
                    $currentNode->getParent()->getParent()->color = Node::COLOR_RED;
                    $currentNode = $currentNode->getParent()->getParent();
                } else {
                    if ($currentNode === $currentNode->getParent()->getLeft()) { // Case 5. Current is left child
                        // Transform into case 6
                        $currentNode = $currentNode->getParent();
                        $this->rotateRight($currentNode);
                    }
                    $currentNode->getParent()->color = Node::COLOR_BLACK; // Case 6. Current is right child.
                    // Re-color father and grandfather, rotate grandfather left
                    $currentNode->getParent()->getParent()->color = Node::COLOR_RED;
                    $this->rotateLeft($currentNode->getParent()->getParent());
                }
            }
        }

        $this->root->color = Node::COLOR_BLACK;
    }

    public function treeDelete(Node $deleteNode)
    {
        if ($deleteNode->getLeft() === $this->nilNode || $deleteNode->getRight() === $this->nilNode) { // delete_node has less then 2 children
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

        if ($cutNode === $this->root) {
            $this->root = $fixNode;
        } else {
            if ($cutNode === $cutNode->getParent()->getLeft()) {
                $cutNode->getParent()->setLeft($fixNode);
            } else {
                $cutNode->getParent()->setRight($fixNode);
            }
            $cutNode->getParent()->updateMax(); // update max property of the parent
        }

        $this->recalcMax($fixNode); // update max property upward from fix_node to root

        // deleteNode becomes cutNode, it means that we cannot hold reference
        // to node in outer structure and we will have to delete by key, additional search need
        if ($cutNode !== $deleteNode) {
            $deleteNode->copyData($cutNode);
            $deleteNode->updateMax(); // update max property of the cut node at the new place
            $this->recalcMax($deleteNode); // update max property upward from deleteNode to root
        }

        if ( /*fix_node !== this.nil_node && */$cutNode->color === Node::COLOR_BLACK) {
            $this->deleteFixup($fixNode);
        }
    }

    public function deleteFixup($fixNode)
    {
        $currentNode = $fixNode;

        while ($currentNode !== $this->root && $currentNode->getParent() !== null && $currentNode->color === Node::COLOR_BLACK) {
            if ($currentNode === $currentNode->getParent()->getLeft()) { // fix node is left child
                $brotherNode = $currentNode->getParent()->getRight();
                if ($brotherNode->color === Node::COLOR_RED) { // Case 1. Brother is red
                    $brotherNode->color = Node::COLOR_BLACK; // re-color brother
                    $currentNode->getParent()->color = Node::COLOR_RED; // re-color father
                    $this->rotateLeft($currentNode->getParent());
                    $brotherNode = $currentNode->getParent()->getRight(); // update brother
                }
                // Derive to cases 2..4: brother is black
                if (
                    $brotherNode->getLeft()->color === Node::COLOR_BLACK &&
                    $brotherNode->getRight()->color === Node::COLOR_BLACK
                ) { // case 2: both nephews black
                    $brotherNode->color = Node::COLOR_RED; // re-color brother
                    $currentNode = $currentNode->getParent(); // continue iteration
                } else {
                    if ($brotherNode->getRight()->color === Node::COLOR_BLACK) { // case 3: left nephew red, right nephew black
                        $brotherNode->color = Node::COLOR_RED; // re-color brother
                        $brotherNode->getLeft()->color = Node::COLOR_BLACK; // re-color nephew
                        $this->rotateRight($brotherNode);
                        $brotherNode = $currentNode->getParent()->getRight(); // update brother
                        // Derive to case 4: left nephew black, right nephew red
                    }
                    // case 4: left nephew black, right nephew red
                    $brotherNode->color = $currentNode->getParent()->color;
                    $currentNode->getParent()->color = Node::COLOR_BLACK;
                    $brotherNode->getRight()->color = Node::COLOR_BLACK;
                    $this->rotateLeft($currentNode->getParent());
                    $currentNode = $this->root; // exit from loop
                }
            } else { // fix node is right child
                $brotherNode = $currentNode->getParent()->getLeft();
                if ($brotherNode->color === Node::COLOR_RED) { // Case 1. Brother is red
                    $brotherNode->color = Node::COLOR_BLACK; // re-color brother
                    $currentNode->getParent()->color = Node::COLOR_RED; // re-color father
                    $this->rotateRight($currentNode->getParent());
                    $brotherNode = $currentNode->getParent()->getLeft(); // update brother
                }
                // Go to cases 2..4
                if (
                    $brotherNode->getLeft()->color === Node::COLOR_BLACK &&
                    $brotherNode->getRight()->color === Node::COLOR_BLACK
                ) { // case 2
                    $brotherNode->color = Node::COLOR_RED; // re-color brother
                    $currentNode = $currentNode->getParent(); // continue iteration
                } else {
                    if ($brotherNode->getLeft()->color === Node::COLOR_BLACK) { // case 3: right nephew red, left nephew black
                        $brotherNode->color = Node::COLOR_RED; // re-color brother
                        $brotherNode->getRight()->color = Node::COLOR_BLACK; // re-color nephew
                        $this->rotateLeft($brotherNode);
                        $brotherNode = $currentNode->getParent()->getLeft(); // update brother
                        // Derive to case 4: right nephew black, left nephew red
                    }
                    // case 4: right nephew black, left nephew red
                    $brotherNode->color = $currentNode->getParent()->color;
                    $currentNode->getParent()->color = Node::COLOR_BLACK;
                    $brotherNode->getLeft()->color = Node::COLOR_BLACK;
                    $this->rotateRight($currentNode->getParent());
                    $currentNode = $this->root; // force exit from loop
                }
            }
        }

        $currentNode->color = Node::COLOR_BLACK;
    }

    public function treeSearch($node, $searchNode)
    {
        if ($node === null || $node === $this->nilNode) {
            return null;
        }

        if ($searchNode->equalTo($node)) {
            return $node;
        }
        if ($searchNode->lessThan($node)) {
            return $this->treeSearch($node->getLeft(), $searchNode);
        } else {
            return $this->treeSearch($node->getRight(), $searchNode);
        }
    }

    // Original search_interval method; container res support push() insertion
    // Search all intervals intersecting given one
    public function treeSearchInterval(Node $node, $searchNode, &$res = [])
    {
        if ($node !== null && $node !== $this->nilNode) {
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
    }

    public function localMinimum(Node $node)
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

    //           |            right-rotate(T,y)       |
    //           y            ---------------.       x
    //          / \                                  / \
    //         x   c          left-rotate(T,x)      a   y
    //        / \             <---------------         / \
    //       a   b                                    b   c

    public function rotateLeft(Node $x)
    {
        $y = $x->getRight();

        $x->setRight($y->getLeft()); // b goes to x.right

        if ($y->getLeft() !== $this->nilNode) {
            $y->getLeft()->setParent($x); // x becomes parent of b
        }
        $y->setParent($x->getParent()); // move parent

        if ($x === $this->root) {
            $this->root = $y; // y becomes root
        } else { // y becomes child of x.parent
            if ($x === $x->getParent()->getLeft()) {
                $x->getParent()->setLeft($y);
            } else {
                $x->getParent()->setRight($y);
            }
        }
        $y->setLeft($x); // x becomes left child of y
        $x->setParent($y); // and y becomes parent of x

        if ($x !== null && $x !== $this->nilNode) {
            $x->updateMax();
        }

        $y = $x->getParent();
        if ($y !== null && $y !== $this->nilNode) {
            $y->updateMax();
        }
    }

    public function rotateRight(Node $y)
    {
        $x = $y->getLeft();

        $y->setLeft($x->getRight()); // b goes to y.left

        if ($x->getRight() !== $this->nilNode) {
            $x->getRight()->setParent($y); // y becomes parent of b
        }
        $x->setParent($y->getParent()); // move parent

        if ($y === $this->root) { // x becomes root
            $this->root = $x;
        } else { // y becomes child of x.parent
            if ($y === $y->getParent()->getLeft()) {
                $y->getParent()->setLeft($x);
            } else {
                $y->getParent()->setRight($x);
            }
        }
        $x->setRight($y); // y becomes right child of x
        $y->setParent($x); // and x becomes parent of y

        if ($y !== null && $y !== $this->nilNode) {
            $y->updateMax();
        }

        $x = $y->getParent();
        if ($x !== null && $x !== $this->nilNode) {
            $y->updateMax();
        }
    }

    public function treeWalk(Node $node, $action)
    {
        if ($node !== null && $node !== $this->nilNode) {
            $this->treeWalk($node->getLeft(), $action);
            // arr.push(node.toArray());
            $action($node);
            $this->treeWalk($node->getRight(), $action);
        }
    }

    /* Return true if all red nodes have exactly two black child nodes */
    public function testRedBlackProperty()
    {
        $res = true;
        $this->treeWalk($this->root, function ($node) use (&$res) {
            if ($node->color === Node::COLOR_RED) {
                if (!($node->getLeft()->color === Node::COLOR_BLACK && $node->getRight()->color === Node::COLOR_BLACK)) {
                    $res = false;
                }
            }
        });
        return $res;
    }

    /* Throw error if not every path from root to bottom has same black height */
    public function testBlackHeightProperty($node)
    {
        $height = 0;
        $heightLeft = 0;
        $heightRight = 0;
        if ($node->color === Node::COLOR_BLACK) {
            $height++;
        }
        if ($node->getLeft() !== $this->nilNode) {
            $heightLeft = $this->testBlackHeightProperty($node->getLeft());
        } else {
            $heightLeft = 1;
        }
        if ($node->getRight() !== $this->nilNode) {
            $heightRight = $this->testBlackHeightProperty($node->getRight());
        } else {
            $heightRight = 1;
        }
        if ($heightLeft !== $heightRight) {
            throw new \Exception('Red-black height property violated');
        }
        $height += $heightLeft;
        return $height;
    }
};
