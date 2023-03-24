<?php

namespace MattiaBasone\FixedWidth\Tests;

use MattiaBasone\FixedWidth\Field;
use MattiaBasone\FixedWidth\FixedWidth;

readonly class FakeDTO implements FixedWidth
{
    public function __construct(
        #[Field(from: 0, to: 9)]
        public string $name,

        #[Field(from: 10, to: 19)]
        public string $surname,

        #[Field(from: 20, to: 22)]
        public int $age,

        #[Field(from: 23, to: 32)]
        public float $salary,

        #[Field(from: 33, to: 35)]
        public string $country,

        #[Field(from: 36, to: 44, align: Field::ALIGN_RIGHT, filler: "#")]
        public string $userCode,
    ) {
    }
}