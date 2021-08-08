<?php
declare(strict_types=1);

namespace Danon\IntervalTree\Tests\Benchmark;

use Danon\IntervalTree\Interval\IntegerInterval;
use Danon\IntervalTree\IntervalTree;
use Exception;
use PhpBench\Benchmark\Metadata\Annotations\Revs;

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
     * @var IntervalTree
     */
    private $tree;

    /**
     * @var IntegerInterval[]
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
        $this->tree->hasIntersection($searchedInterval);
    }

    /**
     * @Revs(1000)
     */
    public function benchBruteForce(): void
    {
        $searchedInterval = $this->generateInterval();
        foreach ($this->bruteForceList as $interval) {
            if ($interval->intersect($searchedInterval)) {
                break;
            }
        }
    }

    /**
     * @return IntegerInterval
     */
    private function generateInterval(): IntegerInterval
    {
        try {
            $low = random_int(0, self::MAX_INTERVAL_HIGH);
            $high = random_int($low, min($low + self::MAX_INTERVAL_OFFSET, self::MAX_INTERVAL_HIGH));
        } catch (Exception $exception) {
            echo 'Cannot generate interval: ' . $exception->getMessage();
            exit;
        }
        return new IntegerInterval($low, $high);
    }
}
