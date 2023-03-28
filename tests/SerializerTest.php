<?php

namespace MattiaBasone\FixedWidth\Tests;

use MattiaBasone\FixedWidth\FixedWidth;
use MattiaBasone\FixedWidth\Serializer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SerializerTest extends TestCase
{
    #[Test]
    #[DataProvider('entitiesDataProvider')]
    public function serializeEntities(FixedWidth $object, string $expected): void
    {
        $serializer = new Serializer();

        $actual = $serializer->serialize($object);

        self::assertSame($expected, $actual);
    }

    public static function entitiesDataProvider(): array
    {
        return [
            [
                new FakeDTO("Mario", "Rossi", "22", "29000", "ITA", "98AB21"),
                "Mario     Rossi     22 29000     ITA###98AB21"
            ],
            [
                new FakeDTO("Mariæ", "Roßi", "22", "33000000", "ITA", "00098AB21"),
                "Mariæ     Roßi      22 33000000  ITA00098AB21"
            ],
        ];
    }
}
