<?php

namespace MattiaBasone\FixedWidth\Tests\FakeObjects;

use MattiaBasone\FixedWidth\FixedWidthProperty;
use MattiaBasone\FixedWidth\FixedWidth;

readonly class FakeDTO implements FixedWidth
{
    public function __construct(
        #[FixedWidthProperty(from: 0, to: 9)]
        public string $name,

        #[FixedWidthProperty(from: 10, to: 19)]
        public string $surname,

        #[FixedWidthProperty(from: 20, to: 22)]
        public int $age,

        #[FixedWidthProperty(from: 23, to: 32)]
        public float $salary,

        #[FixedWidthProperty(from: 33, to: 35)]
        public string $country,

        #[FixedWidthProperty(from: 36, to: 44, align: FixedWidthProperty::ALIGN_RIGHT, filler: "#")]
        public string $userCode,
    ) {
    }
}