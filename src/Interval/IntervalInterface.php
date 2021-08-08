<?php
declare(strict_types=1);

namespace Danon\IntervalTree\Interval;

interface IntervalInterface
{
    // @phpstan-ignore-next-line
    public function __construct($low, $high);

    // @phpstan-ignore-next-line
    public static function fromArray($interval);

    // @phpstan-ignore-next-line
    public function getLow();

    // @phpstan-ignore-next-line
    public function getHigh();

    // @phpstan-ignore-next-line
    public function equalTo($otherInterval): bool;

    // @phpstan-ignore-next-line
    public function lessThan($otherInterval): bool;

    // @phpstan-ignore-next-line
    public function intersect($otherInterval): bool;

    // @phpstan-ignore-next-line
    public function merge($otherInterval);
}
