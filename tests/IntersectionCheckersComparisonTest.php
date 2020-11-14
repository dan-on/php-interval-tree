<?php

declare(strict_types=1);

use Danon\IntervalTree\IntervalTree;
use PHPUnit\Framework\TestCase;

interface IntersectionsChecker
{
    /**
     * Check there are intersections for each Y-interval in X-intervals
     *
     * @param array $xList
     * @param array $yList
     * @return Iterator
     */
    public function checkIntersections(array $xList, array $yList): Iterator;
}

class BruteForceIntersectionsChecker implements IntersectionsChecker
{
    public function checkIntersections(array $xList, array $yList): Iterator
    {
        $checkIntersection = function (array $x, array $y) {
            return max($x[0], $y[0]) <= min($x[1], $y[1]);
        };

        foreach ($yList as $yInterval) {
            $hasIntersection = false;
            foreach ($xList as $xInterval) {
                if ($checkIntersection($xInterval, $yInterval)) {
                    $hasIntersection = true;
                    break;
                }
            }
            yield $hasIntersection;
        }
    }
}

class IntervalTreeIntersectionsChecker implements IntersectionsChecker
{
    public function checkIntersections(array $xList, array $yList): Iterator
    {
        $tree = new IntervalTree();
        foreach ($xList as $xInterval) {
            $tree->insert($xInterval);
        }

        foreach ($yList as $yInterval) {
            yield $tree->hasIntersection($yInterval);
        }
    }
}

final class IntersectionCheckersComparisonTest extends TestCase
{
    const INTERVAL_INDEX_MULTIPLIER = 512;
    const INTERVAL_HIGH_MAX_INCREMENT = 512 / 2 * 3;
    const NUMBER_OF_INTERVALS = 250;

    protected $x = [];
    protected $y = [];
    protected $intersectionCheckers = [];

    function setUp(): void
    {
        $genRandomInterval = function (int $rangeIndex) {

            $low = rand(
                $rangeIndex * self::INTERVAL_INDEX_MULTIPLIER,
                ($rangeIndex + 1) * self::INTERVAL_INDEX_MULTIPLIER
            );
            $high = $low + rand(0, self::INTERVAL_HIGH_MAX_INCREMENT);

            return [$low, $high];
        };

        // Generate random intervals
        $this->x = array_map($genRandomInterval, range(0, self::NUMBER_OF_INTERVALS - 1));
        $this->y = array_map($genRandomInterval, range(0, self::NUMBER_OF_INTERVALS - 1));

        // Instantiate intersection checkers
        $this->intersectionCheckers = [
            new BruteForceIntersectionsChecker,
            new IntervalTreeIntersectionsChecker
        ];
    }


    function testDifferentIntersectionsCheckers()
    {
        // Get check lists for each intersection checker 
        $checkLists = array_map(function (IntersectionsChecker $checker) {
            $checksIterator = $checker->checkIntersections($this->x, $this->y);
            // Convert Y-intersections exists checks into bit-string, like: "01100"
            return implode('', array_map('intval', iterator_to_array($checksIterator)));
        }, $this->intersectionCheckers);

        // They are the same?
        $this->assertGreaterThan(1, count($checkLists));
        $this->assertEquals(1, count(array_unique($checkLists)));
    }
}
