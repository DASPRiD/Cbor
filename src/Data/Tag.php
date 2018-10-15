<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Data;

use DASPRiD\Cbor\CborException;

final class Tag extends DataItem
{
    /**
     * @var int
     */
    private $value;

    public function __construct(int $value)
    {
        if ($value < 0) {
            throw new CborException('Tag number must be 0 or greater');
        }

        parent::__construct(MajorType::TAG());
        $this->value = $value;
    }

    public function getValue() : int
    {
        return $this->value;
    }

    public function equals(DataItem $other) : bool
    {
        return parent::equals($other) && $other instanceof self && $other->value === $this->value;
    }

    public function diagnostic() : string
    {
        return '';
    }

    public function toPhpValue()
    {
        return $this->value;
    }
}
