<?php

namespace MattiaBasone\FixedWidth;

#[\Attribute]
readonly class Field
{
    public const ALIGN_LEFT = 'left';
    public const ALIGN_RIGHT = 'right';

    public function __construct(
        public int $from,
        public int $to,
        public string $align = "left",
        public string $filler = " ",
    ) {
    }

    public function getLength(): int
    {
        return ($this->to - $this->from) + 1;
    }
}