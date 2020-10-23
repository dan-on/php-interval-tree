# PHP Interval tree
Implementation of interval binary search tree.

## Installing
```
composer require dan-on/php-interval-tree
```

## Usage

```php
<?php
require_once 'vendor/autoload.php';

use Danon\IntervalTree\IntervalTree;

$tree = new IntervalTree();
$intervals = [[6, 8], [1, 4], [2, 3], [5, 12], [1, 1], [3, 5], [5, 7]];
        
// Insert interval as a key and interval index as a value 
for ($i=0; $i < count($intervals); $i++) {
    $tree->insert($intervals[$i], $i);
}

// Iterate nodes which keys intersect with given interval
$nodesInRange = $tree->iterateIntersections([2, 3]);
$intersectedIntervalIndexes = [];
foreach ($nodesInRange as $node) {
    $intersectedIntervalIndexes[] = $node->getValue();
}
// Expected array: [1, 2, 5]

// Check that interval has at least one intersection
$tree->hasIntersection([2, 3]);
// Expected value: true

// Count intervals that has intersections
$tree->countIntersections([2, 3]);
// Expected value: 3

// Get array of keys sorted in ascendant order
$sortedIntervals = $tree->getKeys(); 
// Expected array: [[1, 1], [1, 4], [2, 3], [3, 5], [5, 7], [5, 12], [6, 8]]
```

## Tests

```
./vendor/bin/phpunit tests
```
