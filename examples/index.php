<?php
require_once 'vendor/autoload.php';

use Danon\IntervalTree\IntervalTree;
use Danon\IntervalTree\Interval;
use Danon\IntervalTree\Node;

$tree = new IntervalTree();
$intervals = [[6,8],[1,4],[2,3],[5,12],[1,1],[3,5],[5,7]];

// Insert interval as a key and string "val0", "val1" etc. as a value 
for ($i=0; $i < count($intervals); $i++) {
    $tree->insert($intervals[$i], "val" . $i);
}

// Get array of keys sorted in ascendant order
$sorted_intervals = $tree->getKeys(); //  expected array [[1,1],[1,4],[5,7],[5,12],[6,8]]
print_r($sorted_intervals);

// Search items which keys intersect with given interval, and return array of values
$valuesInRange = $tree->iterateIntersections([2,3]);

foreach($valuesInRange as $node) {
    echo $node->getValue() . "\n";
}

echo $tree->hasIntersect([66,83]);
echo $tree->countIntersections([2,3]);



// $tree = new IntervalTree();
// $intervals = [[6,8],[1,4],[2,3],[5,12],[1,1],[3,5],[5,7]];

// // Insert interval as a key and string "val0", "val1" etc. as a value 
// for ($i=0; $i < count($intervals); $i++) {
//     $tree->insert($intervals[$i], "val" . $i);
// }
// $iterator = $tree->searchIterator(new Node(new Interval(2,3)), null);
// foreach($iterator as $node) {
//     echo "123\n";
//     //print_r($node->getValue());
// }

// Array
// (
//     [0] => val1
//     [1] => val2
//     [2] => val5
// )