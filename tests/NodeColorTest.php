<?php

namespace Danon\IntervalTree\Tests;

use Danon\IntervalTree\NodeColor;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Danon\IntervalTree\NodeColor
 */
final class NodeColorTest extends TestCase
{
    public function testBlack(): void
    {
        $nodeColor = NodeColor::black();
        self::assertTrue($nodeColor->isBlack());
    }

    public function testRed(): void
    {
        $nodeColor = NodeColor::red();
        self::assertTrue($nodeColor->isRed());
    }
}
