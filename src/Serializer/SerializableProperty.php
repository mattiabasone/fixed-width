<?php

namespace MattiaBasone\FixedWidth\Serializer;

use MattiaBasone\FixedWidth\Field;

readonly class SerializableProperty
{
    public function __construct(
        public string $propertyName,
        public string $propertyValue,
        public Field  $field
    )
    {
    }
}
