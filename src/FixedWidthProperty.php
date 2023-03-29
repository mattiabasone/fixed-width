<?php

namespace MattiaBasone\FixedWidth;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
readonly class FixedWidthProperty
{
    public const ALIGN_LEFT = 'left';
    public const ALIGN_RIGHT = 'right';

    public function __construct(
        public int $from,
        public int $to,
        public string $align = "left",
        public string $filler = " ",
        public string $encoding = "UTF-8"
    ) {
    }

    public function length(): int
    {
        return ($this->to - $this->from) + 1;
    }
}