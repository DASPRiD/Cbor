<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Builder;

use DASPRiD\Cbor\Data\ArrayList;
use DASPRiD\Cbor\Data\DataItem;
use DASPRiD\Cbor\Data\Map;
use DASPRiD\Cbor\Data\SimpleValue;

final class ArrayBuilder extends Builder
{
    /**
     * @var ArrayList
     */
    private $array;

    public function __construct(?Builder $parent, ArrayList $array)
    {
        parent::__construct($parent);
        $this->array = $array;
    }

    public function add($value) : self
    {
        if (! $value instanceof DataItem) {
            $value = self::convert($value);
        }

        $this->array->add($value);
        return $this;
    }

    public function addByteString(string $value) : self
    {
        $this->add(self::convertBytes($value));
        return $this;
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

    public function end() : Builder
    {
        if ($this->array->isChunked()) {
            $this->add(SimpleValue::break());
        }

        return $this->getParent();
    }
}
