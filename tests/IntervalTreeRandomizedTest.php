<?php

declare(strict_types=1);

namespace Danon\IntervalTree\Tests;

use Danon\IntervalTree\Interval\NumericInterval;
use Danon\IntervalTree\IntervalTree;
use PHPUnit\Framework\TestCase;

/**
 * Compares the tree against a brute-force list under a random mix of
 * inserts and removals.  Intervals are kept distinct so that the test
 * exercises rebalancing and max-augmentation rather than duplicate handling.
 *
 * @covers \Danon\IntervalTree\IntervalTree
 * @uses \Danon\IntervalTree\Interval\NumericInterval
 * @uses \Danon\IntervalTree\Node
 * @uses \Danon\IntervalTree\NodeColor
 * @uses \Danon\IntervalTree\Pair
 */
final class IntervalTreeRandomizedTest extends TestCase
{
    /**
     * @return array<string, array{int}>
     */
    public function provideSeeds(): array
    {
        $seeds = [];
        for ($seed = 1; $seed <= 50; $seed++) {
            $seeds["seed $seed"] = [$seed];
        }
        return $seeds;
    }

    /**
     * @dataProvider provideSeeds
     */
    public function testRandomInsertRemoveMatchesBruteForce(int $seed): void
    {
        mt_srand($seed);
        /** @var IntervalTree<int, int> $tree */
        $tree = new IntervalTree();
        /** @var array<int, array{int, int}> $live */
        $live = [];
        for ($op = 1; $op <= 300; $op++) {
            if (count($live) < 2 || mt_rand(0, 2) > 0) {
                $low = mt_rand(0, 1000) * 1000 + $op;
                $high = $low + mt_rand(0, 30000);
                $tree->insert(new NumericInterval($low, $high), $op);
                $live[$op] = [$low, $high];
            } else {
                $value = array_rand($live);
                [$low, $high] = $live[$value];
                self::assertTrue($tree->remove(new NumericInterval($low, $high), $value));
                unset($live[$value]);
            }

            $queryLow = mt_rand(0, 1030000);
            $queryHigh = $queryLow + mt_rand(0, 20000);
            $expected = [];
            foreach ($live as $value => [$low, $high]) {
                if (!($high < $queryLow || $queryHigh < $low)) {
                    $expected[] = $value;
                }
            }
            $actual = [];
            foreach ($tree->findIntersections(new NumericInterval($queryLow, $queryHigh)) as $pair) {
                $actual[] = $pair->getValue();
            }
            sort($expected);
            sort($actual);
            self::assertSame($expected, $actual, "seed $seed, operation $op");
            self::assertSame(count($live), $tree->getSize());
        }
    }
}
