# PHP Interval tree
[![Latest Stable Version](http://poser.pugx.org/dan-on/php-interval-tree/v)](https://packagist.org/packages/dan-on/php-interval-tree) [![Total Downloads](http://poser.pugx.org/dan-on/php-interval-tree/downloads)](https://packagist.org/packages/dan-on/php-interval-tree) [![License](http://poser.pugx.org/dan-on/php-interval-tree/license)](https://packagist.org/packages/dan-on/php-interval-tree) [![PHP Version Require](http://poser.pugx.org/dan-on/php-interval-tree/require/php)](https://packagist.org/packages/dan-on/php-interval-tree)

## Overview

Package **dan-on/php-interval-tree** is an implementation of self balancing binary search tree data structure called Red-Black Tree.

Based on interval tree described in "Introduction to Algorithms 3rd Edition", published by Thomas H. Cormen, Charles E. Leiserson, Ronald L. Rivest, and Clifford Stein.

## Complexity

| Operation | Best, Average, Worst   |
|-----------|------------------------|
| Insertion | O(log(n))              |
| Search    | O(log(n))              |
| Remove    | O(log(n))              |
| Space     | O(n)                   |

## Installing via Composer

```
composer require dan-on/php-interval-tree
```

## Usage

### Interval Tree

#### insert(IntervalInterface $interval, mixed $value): void
Insert new pair (interval + value) into interval tree
```php
use Danon\IntervalTree\IntervalTree;

$tree = new IntervalTree();
$tree->insert(new NumericInterval(1, 10), 'val1');
$tree->insert(new NumericInterval(2, 5), 'val2');
$tree->insert(new NumericInterval(11, 12), 'val3');
```

#### findIntersections(IntervalInterface $interval): Iterator\<Pair>
Find pairs which intervals intersect with given interval
```php
$intersections = $tree->findIntersections(new NumericInterval(3, 5));
foreach($intersections as $pair) {
    $pair->getInterval()->getLow(); // 1, 2
    $pair->getInterval()->getHigh(); // 10, 5
    $pair->getValue(); // 'val1', 'val2'
}
```

#### hasIntersection(IntervalInterface $interval): bool
Returns true if interval has at least one intersection in tree
```php
$tree->hasIntersection(new NumericInterval(3, 5)); // true
```

#### countIntersections(IntervalInterface $interval): int
Count intersections given interval in tree
```php
$tree->countIntersections(new NumericInterval(3, 5)); // 2
```

#### remove(IntervalInterface $interval, $value): bool
Remove node from tree by interval and value
```php
$tree->remove(new NumericInterval(11, 12), 'val3'); // true
```

#### exist(IntervalInterface $interval, $value): bool
Returns true if interval and value exist in the tree
```php
$tree->exists(new NumericInterval(11, 12), 'val3'); // true
```

#### isEmpty(): bool
Returns true if tree is empty
```php
$tree->isEmpty(); // false
```

#### getSize(): int
Get number of items stored in the interval tree
```php
$tree->getSize(); // 3
```

### Intervals

There are numeric and DateTimeInterface-based interval types included.

#### Numeric interval

```php
use Danon\IntervalTree\Interval\NumericInterval;

// Instantiate numeric interval from array
$numericInterval = NumericInterval::fromArray([1, 100]);

// Instantiate numeric interval with constructor
$numericInterval = new NumericInterval(1, 100);
```

#### DateTime interval
```php
use Danon\IntervalTree\Interval\DateTimeInterval;

// Instantiate DateTime interval from array
$dateTimeInterval = DateTimeInterval::fromArray([
    new DateTimeImmutable('2021-01-01 00:00:00'),
    new DateTimeImmutable('2021-01-02 00:00:00'),
]);

// Instantiate DateTime interval with constructor
$dateTimeInterval = new DateTimeInterval(
    new DateTimeImmutable('2021-01-01 00:00:00'), 
    new DateTimeImmutable('2021-01-02 00:00:00')
);
```

## Tests

```
./vendor/bin/phpunit
```
