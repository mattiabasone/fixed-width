<?php

declare(strict_types=1);

namespace MattiaBasone\FixedWidth\Tests;

use MattiaBasone\FixedWidth\FixedWidth;
use MattiaBasone\FixedWidth\Serializer;
use MattiaBasone\FixedWidth\Serializer\Exception\EntityNotSerializableException;
use MattiaBasone\FixedWidth\Tests\FakeObjects\FakeDTO;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SerializerTest extends TestCase
{
    #[Test]
    #[DataProvider('serializeEntitiesDataProvider')]
    public function serializeEntities(FixedWidth $object, string $expected): void
    {
        $serializer = new Serializer();

        $actual = $serializer->serialize($object);

        self::assertSame($expected, $actual);
    }

    public static function serializeEntitiesDataProvider(): array
    {
        return [
            [
                new FakeDTO("Mario", "Rossi", 22, 29000, "ITA", "98AB21"),
                "Mario     Rossi     22 29000     ITA###98AB21",
            ],
            [
                new FakeDTO("Mariæ", "Roßi", 22, 33000000, "ITA", "00098AB21"),
                "Mariæ     Roßi      22 33000000  ITA00098AB21",
            ],
        ];
    }

    #[Test]
    public function cannotSerializeEntityWithoutAttributes(): void
    {
        $this->expectException(EntityNotSerializableException::class);

        $serializer = new Serializer();

        $serializer->serialize(new class implements FixedWidth{
            private string $field1;
        });
    }

    #[Test]
    #[DataProvider('deserializeEntitiesDataProvider')]
    public function deserializeEntities(string $object, FixedWidth $expected): void
    {
        $serializer = new Serializer();

        $actual = $serializer->deserialize($object, $expected::class);

        self::assertEquals($expected, $actual);
    }

    public static function deserializeEntitiesDataProvider(): array
    {
        return [
            [
                "Mario     Rossi     22 29000     ITA###98AB21",
                new FakeDTO("Mario", "Rossi", 22, 29000, "ITA", "98AB21"),
            ],
            [
                "Mariæ     Roßi      22 33000000  ITA00098AB21",
                new FakeDTO("Mariæ", "Roßi", 22, 33000000, "ITA", "00098AB21"),
            ],
        ];
    }

    #[Test]
    public function cannotDeserializeEntityWithoutAttributes(): void
    {
        $this->expectException(EntityNotSerializableException::class);

        $class = new class implements FixedWidth{
            private string $field1;
        };

        $serializer = new Serializer();

        $serializer->deserialize("", $class::class);
    }
}
