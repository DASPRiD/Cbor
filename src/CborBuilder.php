<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor;

use DASPRiD\Cbor\Builder\ArrayBuilder;
use DASPRiD\Cbor\Builder\Builder;
use DASPRiD\Cbor\Builder\ByteStringBuilder;
use DASPRiD\Cbor\Builder\MapBuilder;
use DASPRiD\Cbor\Builder\UnicodeStringBuilder;
use DASPRiD\Cbor\Data\ArrayList;
use DASPRiD\Cbor\Data\ByteString;
use DASPRiD\Cbor\Data\DataItem;
use DASPRiD\Cbor\Data\Map;
use DASPRiD\Cbor\Data\UnicodeString;

final class CborBuilder extends Builder
{
    /**
     * @var DataItem[]
     */
    private $dataItems = [];

    public function __construct()
    {
        parent::__construct(null);
    }

    public function reset() : void
    {
        $this->dataItems = [];
    }

    /**
     * @return DataItem[]
     */
    public function build() : array
    {
        return $this->dataItems;
    }

    public function add($value) : self
    {
        if (! $value instanceof DataItem) {
            $value = self::convert($value);
        }

        $this->dataItems = $value;
        return $this;
    }

    public function addByteString(string $value) : self
    {
        $this->add(self::convertBytes($value));
        return $this;
    }

    public function addTag(int $value) : self
    {
        $this->add(self::convertTag($value));
        return $this;
    }

    public function startByteString(?string $bytes = null) : ByteStringBuilder
    {
        $byteString = new ByteString($bytes);
        $byteString->setChunked(true);
        $this->add($byteString);
        return new ByteStringBuilder($this);
    }

    public function startString(?string $string = null) : UnicodeStringBuilder
    {
        $byteString = new UnicodeString($string);
        $byteString->setChunked(true);
        $this->add($string);
        return new UnicodeStringBuilder($this);
    }

    public function startArray() : ArrayBuilder
    {
        $array = new ArrayList();
        $array->setChunked(true);
        $this->add($array);
        return new ArrayBuilder($this, $array);
    }

    public function addArray() : ArrayBuilder
    {
        $array = new ArrayList();
        $this->add($array);
        return new ArrayBuilder($this, $array);
    }

    public function startMap() : MapBuilder
    {
        $map = new Map();
        $map->setChunked(true);
        $this->add($map);
        return new MapBuilder($this, $map);
    }

    public function addMap() : MapBuilder
    {
        $map = new Map();
        $this->add($map);
        return new MapBuilder($this, $map);
    }

    protected function addChunk(DataItem $dataItem) : void
    {
        $this->add($dataItem);
    }
}
