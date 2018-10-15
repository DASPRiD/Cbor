# CBOR

[![Build Status](https://travis-ci.org/DASPRiD/Cbor.svg?branch=master)](https://travis-ci.org/DASPRiD/Cbor)
[![Coverage Status](https://coveralls.io/repos/github/DASPRiD/Cbor/badge.svg?branch=master)](https://coveralls.io/github/DASPRiD/Cbor?branch=master)
[![Latest Stable Version](https://poser.pugx.org/dasprid/cbor/v/stable)](https://packagist.org/packages/dasprid/cbor)
[![Total Downloads](https://poser.pugx.org/dasprid/cbor/downloads)](https://packagist.org/packages/dasprid/cbor)
[![License](https://poser.pugx.org/dasprid/cbor/license)](https://packagist.org/packages/dasprid/cbor)

A PHP implementation of [RFC 7049](http://tools.ietf.org/html/rfc7049):
Concise Binary Object Representation ([CBOR](http://cbor.io/))

## Features

- Encodes and decodes all examples described in RFC 7049
- Provides a fluent interface builder for CBOR messages
- Supports semantic tags
- Supports 64bit integer values

## Usage

### Encoding Example
```php
<?php
$output = new \SplTempFileObject();

(new \DASPRiD\Cbor\CborEncoder($output))->encode((new \DASPRiD\Cbor\CborBuilder())
    ->add('text')
    ->add(1234)
    ->addByteString("\x10")
    ->addArray()
        ->add(1)
        ->add('text')
        ->end()
    ->build()
);

$length = $output->ftell();
$output->rewind();
$encodedBytes = $output->fread($length);
```

### Decoding Example
```php
<?php
$dataItems = \DASPRiD\Cbor\CborDecoder::decodeString($encodedBytes);

foreach ($dataItems as $dataItem) {
    // Process data item
}
```
