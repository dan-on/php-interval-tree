<?php

declare(strict_types=1);

namespace Danon\IntervalTree\Tests\Benchmark;

use Danon\IntervalTree\Interval\NumericInterval;
use Exception;
use InvalidArgumentException;

trait GenerateIntervalTrait
{
    /**
     * @param int $maxHigh
     * @param int $maxOffset
     * @return NumericInterval
     */
    private function generateInterval(int $maxHigh, int $maxOffset): NumericInterval
    {
        try {
            $low = random_int(0, $maxHigh);
            $high = random_int($low, min($low + $maxOffset, $maxHigh));
        } catch (Exception $exception) {
            throw new InvalidArgumentException('Wrong interval arguments', $exception->getCode(), $exception);
        }
        return new NumericInterval($low, $high);
    }
}
