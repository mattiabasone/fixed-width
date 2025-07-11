<?php

declare(strict_types=1);

namespace MattiaBasone\FixedWidth;

use MattiaBasone\FixedWidth\Serializer\Exception\EntityNotSerializableException;
use MattiaBasone\FixedWidth\Serializer\ObjectPropertyData;
use MattiaBasone\FixedWidth\Serializer\ObjectStructure;

final class Serializer
{
    /**
     * @var array<ObjectStructure>
     */
    private static array $objectStructureCache = [];

    /**
     * @throws EntityNotSerializableException
     */
    public function serialize(FixedWidth $entity): string
    {
        $objectStructure = self::getObjectStructure($entity::class);

        if (!$objectStructure->hasFixedWidthProperties()) {
            throw EntityNotSerializableException::noFixedWidthPropertyAttributes();
        }

        $row = "";
        foreach ($objectStructure->properties as $property) {
            $row .= mb_str_pad(
                (string) $property->valueForEntity($entity),
                $property->attribute->length(),
                $property->attribute->filler,
                $property->attribute->align === FixedWidthProperty::ALIGN_LEFT ? \STR_PAD_RIGHT : \STR_PAD_LEFT,
                $property->attribute->encoding
            );
        }

        return $row;
    }

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function deserialize(string $row, string $type): object
    {
        $objectStructure = self::getObjectStructure($type);

        if (!$objectStructure->hasFixedWidthProperties()) {
            throw EntityNotSerializableException::noFixedWidthPropertyAttributes();
        }

        $instance = $objectStructure->reflectionClass->newInstanceWithoutConstructor();
        foreach ($objectStructure->properties as $property) {
            $objectStructure
                ->reflectionClass
                ->getProperty($property->name())
                ->setValue($instance, self::castPropertyValue($row, $property));
        }

        return $instance;
    }

    private static function getObjectStructure(string $objectClass): ObjectStructure
    {
        if (array_key_exists($objectClass, self::$objectStructureCache)) {
            return self::$objectStructureCache[$objectClass];
        }

        /** @psalm-suppress ArgumentTypeCoercion */
        $class = new \ReflectionClass($objectClass);

        $properties = [];
        foreach ($class->getProperties() as $reflectionProperty) {
            $fieldData = $reflectionProperty->getAttributes(FixedWidthProperty::class)[0] ?? null;
            if ($fieldData === null) {
                continue;
            }

            $attribute = $fieldData->newInstance();

            $properties[] = new ObjectPropertyData(
                $reflectionProperty,
                $attribute,
            );
        }

        $properties = self::sortPropertiesByPosition($properties);

        self::$objectStructureCache[$objectClass] = new ObjectStructure($class, $properties);

        return self::$objectStructureCache[$objectClass];
    }

    /**
     * @param array<int, ObjectPropertyData> $properties
     * @return array<int, ObjectPropertyData> $properties
     */
    private static function sortPropertiesByPosition(array $properties): array
    {
        usort($properties, fn (ObjectPropertyData $first, ObjectPropertyData $second) => $first->attribute->from <=> $second->attribute->from);

        return $properties;
    }

    /**
     * @throws \Exception
     */
    private static function castPropertyValue(string $row, ObjectPropertyData $propertyData): string|int|float
    {
        $value = mb_substr($row, $propertyData->attribute->from, $propertyData->attribute->length(), $propertyData->attribute->encoding);
        $value = trim($value, $propertyData->attribute->filler);
        return self::castValue($value, $propertyData->type());
    }

    private static function castValue(string $value, ?string $typeName): string|int|float
    {
        return match ($typeName) {
            "string" => $value,
            "int" => (int) $value,
            "float" => (float) $value,
            default => throw new \Exception("Invalid property type"),
        };
    }
}
