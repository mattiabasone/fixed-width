<?php

namespace MattiaBasone\FixedWidth\Serializer;

use MattiaBasone\FixedWidth\FixedWidthProperty;

readonly class SerializableProperty
{
    public function __construct(
        public string $propertyName,
        public string $propertyValue,
        public FixedWidthProperty $field
    )
    {
    }
}
