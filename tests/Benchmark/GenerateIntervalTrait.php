<?php
declare(strict_types=1);

namespace Danon\IntervalTree\Tests\Benchmark;

use Danon\IntervalTree\Interval;
use Exception;

trait GenerateIntervalTrait
{
    /**
     * @return Interval
     */
    private function generateInterval(): Interval
    {
        try {
            $low = random_int(0, self::MAX_INTERVAL_HIGH);
            $high = random_int($low, min($low + self::MAX_INTERVAL_OFFSET, self::MAX_INTERVAL_HIGH));
        } catch (Exception $exception) {
            echo 'Cannot generate interval: ' . $exception->getMessage();
            exit;
        }
        return new Interval($low, $high);
    }
}