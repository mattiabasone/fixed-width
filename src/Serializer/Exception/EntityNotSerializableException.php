<?php

declare(strict_types=1);

namespace MattiaBasone\FixedWidth\Serializer\Exception;

use MattiaBasone\FixedWidth\FixedWidthProperty;

/**
 * @internal
 */
final class EntityNotSerializableException extends \Exception
{
    public static function noFixedWidthPropertyAttributes(): self
    {
        return new self("No properties found with attribute ".FixedWidthProperty::class);
    }
}
