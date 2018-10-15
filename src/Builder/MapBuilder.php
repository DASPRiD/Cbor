<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Builder;

use DASPRiD\Cbor\Data\ArrayList;
use DASPRiD\Cbor\Data\DataItem;
use DASPRiD\Cbor\Data\Map;

final class MapBuilder extends Builder
{
    /**
     * @var Map
     */
    private $map;

    public function __construct(?Builder $parent, Map $map)
    {
        parent::__construct($parent);
        $this->map = $map;
    }

    public function put($key, $value) : self
    {
        if (! $key instanceof DataItem) {
            $key = self::convert($key);
        }

        if (! $value instanceof DataItem) {
            $value = self::convert($value);
        }

        $this->map[$key] = $value;
        return $this;
    }

    public function putByteString($key, string $value) : self
    {
        $this->put($key, self::convertBytes($value));
        return $this;
    }

    public function startArray($key) : ArrayBuilder
    {
        $array = new ArrayList();
        $array->setChunked(true);
        $this->put($key, $array);
        return new ArrayBuilder($this, $array);
    }

    public function addArray($key) : ArrayBuilder
    {
        $array = new ArrayList();
        $this->put($key, $array);
        return new ArrayBuilder($this, $array);
    }

    public function startMap($key) : MapBuilder
    {
        $map = new Map();
        $map->setChunked(true);
        $this->put($key, $map);
        return new MapBuilder($this, $map);
    }

    public function addMap($key) : MapBuilder
    {
        $map = new Map();
        $this->put($key, $map);
        return new MapBuilder($this, $map);
    }

    public function end() : Builder
    {
        return $this->getParent();
    }
}
