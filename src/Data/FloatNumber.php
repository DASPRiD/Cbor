<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Data;

abstract class FloatNumber extends Special
{
    /**
     * @var float
     */
    protected $value;

    public function __construct(SpecialType $specialType, float $value)
    {
        parent::__construct($specialType);
        $this->value = $value;
    }

    public function equals(DataItem $other) : bool
    {
        return parent::equals($other) && $other instanceof self && $other->value === $this->value;
    }

    public function getValue() : float
    {
        return $this->value;
    }

    public function diagnostic() : string
    {
        if (is_nan($this->value)) {
            return 'NaN';
        }

        if (is_infinite($this->value)) {
            return sprintf('%sInfinity', $this->value === INF ? '' : '-');
        }

        return preg_replace('((\.\d*?)0+)', '\1', sprintf('%f', $this->value));
    }

    public function toPhpValue()
    {
        return $this->value;
    }
}
