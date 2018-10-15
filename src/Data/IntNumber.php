<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Data;

use Brick\Math\BigInteger;

abstract class IntNumber extends DataItem
{
    /**
     * @var BigInteger
     */
    protected $value;

    public function __construct(MajorType $majorType, BigInteger $value)
    {
        parent::__construct($majorType);
        $this->value = $value;
    }

    public function getValue() : BigInteger
    {
        return $this->value;
    }

    public function equals(DataItem $other) : bool
    {
        return parent::equals($other) && $other instanceof self && $other->value->isEqualTo($this->value);
    }

    public function diagnostic() : string
    {
        return (string) $this->value;
    }

    public function toPhpValue()
    {
        return $this->value;
    }
}
