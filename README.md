# fixed-width

[![Coverage Status](https://coveralls.io/repos/github/mattiabasone/fixed-width/badge.svg)](https://coveralls.io/github/mattiabasone/fixed-width)

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
        #[FixedWidthProperty(from: 0, to: 9)]
        public string $name,

        #[FixedWidthProperty(from: 10, to: 19)]
        public string $surname,
        
        #[FixedWidthProperty(from: 20, to: 22)]
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

For deserialize a string into an object that implements `FixedWidth` interface and has properties with `#[FixedWidthProperty]`:

```php
<?php

use MattiaBasone\FixedWidth\Serializer;
use MyNameSpace\MyObject;

$object = "John      Smith     39 ";

var_dump((new Serializer())->deserialize($object, MyObject::class));

// Prints:
// class MyObject#12 (3) {
//   public string $name =>
//   string(4) "John"
//   public string $surname =>
//   string(5) "Smith"
//   public int $age =>
//   int(39)
// }
```
