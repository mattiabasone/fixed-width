<?php

namespace MattiaBasone\FixedWidth;

use MattiaBasone\FixedWidth\Serializer\Exception\EntityNotSerializableException;
use MattiaBasone\FixedWidth\Serializer\SerializableProperty;
use MattiaBasone\FixedWidth\Serializer\ObjectPropertyData;
use MattiaBasone\FixedWidth\Serializer\ObjectStructure;

class Serializer
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
            throw EntityNotSerializableException::noFieldAttributes();
        }

        $properties = [];
        foreach ($objectStructure->properties as $property) {
            $properties[] = new SerializableProperty(
                $property->name(),
                (string) $property->valueForEntity($entity),
                $property->attribute
            );
        }

        $properties = self::sortPropertiesByPosition($properties);

        return array_reduce(
            $properties,
            self::serializePropertyCallback(),
            ""
        );
    }

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function deserialize(string $row, string $type): object
    {
        $objectStructure = self::getObjectStructure($type);

        if (!$objectStructure->hasFixedWidthProperties()) {
            throw EntityNotSerializableException::noFieldAttributes();
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

        $class = new \ReflectionClass($objectClass);

        $properties = [];
        foreach ($class->getProperties() as $reflectionProperty) {
            $fieldData = $reflectionProperty->getAttributes(FixedWidthProperty::class)[0] ?? null;
            if ($fieldData === null) {
                continue;
            }

            /** @var FixedWidthProperty $attribute */
            $attribute = $fieldData->newInstance();

            $properties[] = new ObjectPropertyData(
                $reflectionProperty,
                $attribute,
            );
        }

        self::$objectStructureCache[$objectClass] = new ObjectStructure($class, $properties);

        return self::$objectStructureCache[$objectClass];
    }

    private static function multibyteStringPad(
        string $input,
        int $pad_length,
        string $pad_string = " ",
        int $pad_style = STR_PAD_RIGHT,
        string $encoding = "UTF-8"
    ): string {
        return str_pad(
            $input,
            strlen($input) - mb_strlen($input, $encoding) + $pad_length,
            $pad_string,
            $pad_style
        );
    }

    private static function sortPropertiesByPosition(array $properties): array
    {
        usort($properties, fn (SerializableProperty $first, SerializableProperty $second) => $first->field->from <=> $second->field->from);

        return $properties;
    }

    private static function serializePropertyCallback(): \Closure
    {
        return fn (string $accumulator, SerializableProperty $serializableProperty) => $accumulator.
            self::multibyteStringPad(
                $serializableProperty->propertyValue,
                $serializableProperty->field->length(),
                $serializableProperty->field->filler,
                $serializableProperty->field->align === FixedWidthProperty::ALIGN_LEFT ? STR_PAD_RIGHT : STR_PAD_LEFT,
                $serializableProperty->field->encoding
            );
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
