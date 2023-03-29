<?php

namespace MattiaBasone\FixedWidth\Serializer;

use MattiaBasone\FixedWidth\FixedWidth;
use MattiaBasone\FixedWidth\FixedWidthProperty;

readonly class ObjectPropertyData
{
    public function __construct(
        public \ReflectionProperty $reflectionProperty,
        public FixedWidthProperty $attribute
    ) {
    }

    public function name(): string
    {
        return $this->reflectionProperty->getName();
    }

    public function type(): string
    {
        return $this->reflectionProperty->getType()?->getName();
    }

    public function valueForEntity(FixedWidth $object): mixed
    {
        return $this->reflectionProperty->getValue($object);
    }
}