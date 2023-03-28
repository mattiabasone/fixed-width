# fixed-width

This package provides some utilities for generating/parsing fixed-width (positional) files.
This library is under development, use it in production at your own risk :D.

## Installation

`composer require mattiabasone/fixed-width`

## Usage

Given that sample object:

```php
<?php

namespace MyNameSpace;

use MattiaBasone\FixedWidth\FixedWidth;

class MyObject implements FixedWidth
{
    public function __construct(
        #[Field(from: 0, to: 9)]
        public string $name,

        #[Field(from: 10, to: 19)]
        public string $surname,
        
        #[Field(from: 20, to: 22)]
        public int $age
    ) {
    
    }
}

```

You can pass this object to the `serialize()` method of the `Serializer` class.

```php
<?php

use MattiaBasone\FixedWidth\Serializer;
use MyNameSpace\MyObject;

$object = new MyObject("John", "Smith", "39");

echo (new Serializer())->serialize($object);

// Prints
// "John      Smith     39 "
```