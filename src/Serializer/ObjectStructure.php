<?php

namespace MattiaBasone\FixedWidth\Serializer;

readonly class ObjectStructure
{
    /**
     * @param array<ObjectPropertyData> $properties
     */
    public function __construct(
        public \ReflectionClass $reflectionClass,
        public array $properties,
    ) {}

    public function hasFixedWidthProperties(): bool
    {
        return count($this->properties) > 0;
    }
}