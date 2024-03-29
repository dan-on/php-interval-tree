<?php

declare(strict_types=1);

namespace Danon\IntervalTree\Tests\Benchmark;

use Danon\IntervalTree\IntervalTree;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use Danon\IntervalTree\Interval\IntervalInterface;

/**
 * @BeforeMethods({"init"})
 */
class HasIntersectionBench
{
    use GenerateIntervalTrait;

    private const AMOUNT_INTERVALS_IN_TREE = 10000;
    private const MAX_INTERVAL_HIGH        = 250000;
    private const MAX_INTERVAL_OFFSET      = 100;

    /**
     * @var IntervalTree<int|float, null>
     */
    private $tree;

    /**
     * @var IntervalInterface<int|float>[]
     */
    private $bruteForceList;

    public function init(): void
    {
        $this->tree = new IntervalTree();
        $this->bruteForceList = [];

        for ($i = 0; $i < self::AMOUNT_INTERVALS_IN_TREE; $i++) {
            $interval = $this->generateInterval(self::MAX_INTERVAL_HIGH, self::MAX_INTERVAL_OFFSET);
            $this->tree->insert($interval);
            $this->bruteForceList[] = $interval;
        }
    }

    /**
     * @Revs(10)
     */
    public function benchTree(): void
    {
        $searchedInterval = $this->generateInterval(self::MAX_INTERVAL_HIGH, self::MAX_INTERVAL_OFFSET);
        $this->tree->hasIntersection($searchedInterval);
    }

    /**
     * @Revs(10)
     */
    public function benchBruteForce(): void
    {
        $searchedInterval = $this->generateInterval(self::MAX_INTERVAL_HIGH, self::MAX_INTERVAL_OFFSET);
        foreach ($this->bruteForceList as $interval) {
            if ($interval->intersect($searchedInterval)) {
                break;
            }
        }
    }
}
