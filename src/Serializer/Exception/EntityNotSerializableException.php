<?php

namespace MattiaBasone\FixedWidth\Serializer\Exception;

use MattiaBasone\FixedWidth\FixedWidthProperty;

class EntityNotSerializableException extends \Exception
{
    public static function noFieldAttributes(): self
    {
        return new self("No properties found with attribute ".FixedWidthProperty::class);
    }
}
