<?php
declare(strict_types=1);

namespace Danon\IntervalTree\Tests\Benchmark;

use Danon\IntervalTree\Interval\NumericInterval;
use Danon\IntervalTree\IntervalTree;
use PhpBench\Benchmark\Metadata\Annotations\Revs;

/**
 * @BeforeMethods({"init"})
 */
class CountIntersectionsBench
{
    use GenerateIntervalTrait;

    private const AMOUNT_INTERVALS_IN_TREE = 10000;
    private const MAX_INTERVAL_HIGH        = 250000;
    private const MAX_INTERVAL_OFFSET      = 100;

    /**
     * @var IntervalTree
     */
    private $tree;

    /**
     * @var NumericInterval[]
     */
    private $bruteForceList;

    public function init(): void
    {
        $this->tree = new IntervalTree();
        $this->bruteForceList = [];

        for ($i = 0; $i < self::AMOUNT_INTERVALS_IN_TREE; $i++) {
            $interval = $this->generateInterval();
            $this->tree->insert($interval);
            $this->bruteForceList[] = $interval;
        }
    }

    /**
     * @Revs(1000)
     */
    public function benchTree(): void
    {
        $searchedInterval = $this->generateInterval();
        $this->tree->countIntersections($searchedInterval);
    }

    /**
     * @Revs(1000)
     */
    public function benchBruteForce(): void
    {
        $searchedInterval = $this->generateInterval();
        foreach ($this->bruteForceList as $interval) {
            $interval->intersect($searchedInterval);
        }
    }
}
