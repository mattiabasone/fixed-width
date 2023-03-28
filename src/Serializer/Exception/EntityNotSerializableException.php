<?php

namespace MattiaBasone\FixedWidth\Serializer\Exception;

use MattiaBasone\FixedWidth\Field;

class EntityNotSerializableException extends \Exception
{
    public static function noFieldAttributes(): self
    {
        return new self("No properties found with attribute ".Field::class);
    }
}
