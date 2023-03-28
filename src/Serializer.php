<?php

namespace MattiaBasone\FixedWidth;

use MattiaBasone\FixedWidth\Serializer\Exception\EntityNotSerializableException;
use MattiaBasone\FixedWidth\Serializer\FixedWidthProperty;

class Serializer
{
    /**
     * @throws \ReflectionException|EntityNotSerializableException
     */
    public function serialize(FixedWidth $entity): string
    {
        $class = new \ReflectionClass($entity::class);

        $properties = [];
        foreach ($class->getProperties() as $property) {
            $fieldData = $property->getAttributes(Field::class)[0] ?? null;
            if ($fieldData === null) {
                continue;
            }

            $properties[] = new FixedWidthProperty(
                $property->getName(),
                (string) $property->getValue($entity),
                $fieldData->newInstance()
            );
        }

        if ($properties === []) {
            throw EntityNotSerializableException::noFieldAttributes();
        }

        $properties = self::sortPropertiesByPosition($properties);

        return array_reduce(
            $properties,
            fn (string $accumulator, FixedWidthProperty $serializableProperty) => $accumulator.
                self::multibyteStringPad(
                    $serializableProperty->propertyValue,
                    $serializableProperty->field->getLength(),
                    $serializableProperty->field->filler,
                    $serializableProperty->field->align === Field::ALIGN_LEFT ? STR_PAD_RIGHT : STR_PAD_LEFT
                ),
            ""
        );
    }

    public function deserialize(string $row): object
    {
        return new \stdClass();
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
            strlen($input)-mb_strlen($input, $encoding) + $pad_length,
            $pad_string,
            $pad_style
        );
    }

    private static function sortPropertiesByPosition(array $properties): array
    {
        usort($properties, fn (FixedWidthProperty $first, FixedWidthProperty $second) => $first->field->from <=> $second->field->from);

        return $properties;
    }
}
