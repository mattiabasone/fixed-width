<?php

namespace MattiaBasone\FixedWidth\Serializer\Exception;

use MattiaBasone\FixedWidth\FixedWidthProperty;

class EntityNotSerializableException extends \Exception
{
    public static function noFixedWidthPropertyAttributes(): self
    {
        return new self("No properties found with attribute ".FixedWidthProperty::class);
    }
}
