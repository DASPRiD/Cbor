<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Data;

use ArrayAccess;
use Countable;
use DASPRiD\Cbor\CborException;
use IteratorAggregate;
use IteratorIterator;
use SplObjectStorage;
use Traversable;
use UnexpectedValueException;

final class Map extends ChunkableDataItem implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * @var SplObjectStorage
     */
    private $map;

    /**
     * @var bool
     */
    private $rejectDuplicateKeys;

    public function __construct(bool $rejectDuplicateKeys = false)
    {
        parent::__construct(MajorType::MAP());
        $this->map = new SplObjectStorage();
        $this->rejectDuplicateKeys = $rejectDuplicateKeys;
    }

    public function offsetExists($offset) : bool
    {
        return $this->map->offsetExists($offset);
    }

    /**
     * @throws UnexpectedValueException When item is missing
     */
    public function offsetGet($offset): DataItem
    {
        return $this->map->offsetGet($offset);
    }

    public function offsetSet($offset, $value) : void
    {
        if ($this->rejectDuplicateKeys) {
            foreach ($this->map as $key) {
                assert($key instanceof DataItem);

                if ($key->equals($offset)) {
                    throw new CborException('Duplicate key found in map');
                }
            }
        }

        $this->map->offsetSet($offset, $value);
    }

    public function offsetUnset($offset) : void
    {
        $this->map->offsetUnset($offset);
    }

    public function count() : int
    {
        return count($this->map);
    }

    public function getIterator() : Traversable
    {
        return new IteratorIterator($this->map);
    }

    public function equals(DataItem $other) : bool
    {
        if (! parent::equals($other) || ! $other instanceof self) {
            return false;
        }

        if (count($other->map) !== count($this->map)) {
            return false;
        }

        foreach ($this->map as $key) {
            if (! $other->map->offsetExists($key)) {
                return false;
            }

            if ($other->map[$key] !== $this->map[$key]) {
                return false;
            }
        }

        return true;
    }

    public function diagnostic() : string
    {
        $data = [];

        foreach ($this->map as $key) {
            $data[] = sprintf('%s: %s', $key, $this->map[$key]);
        }

        return sprintf('{%s}', implode(', ', $data));
    }

    public function toPhpValue()
    {
        $result = [];

        foreach ($this->map as $key) {
            if (! $key instanceof UnicodeString && ! $key instanceof IntNumber) {
                throw new CborException(
                    'Maps converted to PHP values must only contain UnicodeString or IntNumber keys'
                );
            }

            $result[$key->toPhpValue()] = $this->map[$key]->toPhpValue();
        }

        return $result;
    }
}
