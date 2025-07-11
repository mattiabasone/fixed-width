<?php

declare(strict_types=1);

namespace MattiaBasone\FixedWidth\Serializer;

use MattiaBasone\FixedWidth\FixedWidth;
use MattiaBasone\FixedWidth\FixedWidthProperty;

/**
 * @internal
 */
final readonly class ObjectPropertyData
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
        $type = $this->reflectionProperty->getType();
        return match ($type::class) {
            \ReflectionNamedType::class => $type->getName(),
            \ReflectionIntersectionType::class => throw new \LogicException("{$this->name()} invalid type - Intersection type is not supported"),
            \ReflectionUnionType::class => throw new \LogicException("{$this->name()} invalid type - Union type is not supported"),
        };
    }

    public function valueForEntity(FixedWidth $object): mixed
    {
        return $this->reflectionProperty->getValue($object);
    }
}