<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Data;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

class ArrayList extends ChunkableDataItem implements Countable, IteratorAggregate
{
    /**
     * @var DataItem[]
     */
    private $dataItems = [];

    public function __construct()
    {
        parent::__construct(MajorType::ARRAY());
    }

    public function add(DataItem $value) : void
    {
        $this->dataItems[] = $value;
    }

    /**
     * @return DataItem[]
     */
    public function getDataItems() : array
    {
        return $this->dataItems;
    }

    public function count() : int
    {
        return count($this->dataItems);
    }

    public function getIterator() : Traversable
    {
        return new ArrayIterator($this->dataItems);
    }

    public function equals(DataItem $other) : bool
    {
        return parent::equals($other) && $other instanceof self && $other->dataItems === $this->dataItems;
    }

    public function diagnostic() : string
    {
        return sprintf('(%s)', implode(', ', array_map('strval', $this->dataItems)));
    }

    public function toPhpValue()
    {
        return array_map(function (DataItem $dataItem) {
            return $dataItem->toPhpValue();
        }, $this->dataItems);
    }
}
