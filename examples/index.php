<?php
require_once 'vendor/autoload.php';

use Danon\IntervalTree\IntervalTree;

$tree = new IntervalTree();
$intervals = [[6,8],[1,4],[2,3],[5,12],[1,1],[3,5],[5,7]];

// Insert interval as a key and string "val0", "val1" etc. as a value 
for ($i=0; $i < count($intervals); $i++) {
    $tree->insert($intervals[$i], "val" . $i);
}

// Get array of keys sorted in ascendant order
$sorted_intervals = $tree->getKeys(); //  expected array [[1,1],[1,4],[5,7],[5,12],[6,8]]

// Search items which keys intersect with given interval, and return array of values
$valuesInRange = $tree->search([2,3], function($value, $key) {
    return $value;
});

print_r($valuesInRange);

// Array
// (
//     [0] => val1
//     [1] => val2
//     [2] => val5
// )