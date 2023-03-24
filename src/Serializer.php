<?php

namespace MattiaBasone\FixedWidth;

use MattiaBasone\FixedWidth\Serializer\SerializableProperty;

class Serializer
{
    public function serialize(FixedWidth $entity): string
    {
        $class = new \ReflectionClass($entity::class);

        $properties = [];
        foreach ($class->getProperties() as $property) {
            $fieldData = $property->getAttributes(Field::class)[0] ?? null;
            if ($fieldData === null) {
                continue;
            }

            $properties[] = new SerializableProperty(
                $property->getName(),
                (string) $property->getValue($entity),
                $fieldData->newInstance()
            );
        }

        usort($properties, fn (SerializableProperty $first, SerializableProperty $second) => $first->field->from <=> $second->field->from);

        return array_reduce(
            $properties,
            function (string $accumulator, SerializableProperty $serializableProperty) {
                $padType = $serializableProperty->field->align === Field::ALIGN_LEFT ? STR_PAD_RIGHT : STR_PAD_LEFT;
                $padLength = $serializableProperty->field->getLength();

                return $accumulator.str_pad($serializableProperty->propertyValue, $padLength, $serializableProperty->field->filler, $padType);
            },
            ""
        );
    }
}
