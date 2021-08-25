<?php
declare(strict_types=1);

namespace Danon\IntervalTree;

final class NodeColor
{
    public const COLOR_RED = 0;
    public const COLOR_BLACK = 1;

    /**
     * @var int
     */
    protected $color;

    private function __construct()
    {
    }

    public static function red(): self
    {
        $self = new self();
        $self->color = self::COLOR_RED;

        return $self;
    }

    public static function black(): self
    {
        $self = new self();
        $self->color = self::COLOR_BLACK;

        return $self;
    }

    public function isRed(): bool
    {
        return $this->color === self::COLOR_RED;
    }

    public function isBlack(): bool
    {
        return $this->color === self::COLOR_BLACK;
    }
}
