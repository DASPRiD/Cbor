<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Data;

final class SimpleValue extends Special
{
    /**
     * @var SimpleValueType
     */
    private $simpleValueType;

    /**
     * @var int
     */
    private $value;

    /**
     * @var array self[]
     */
    private static $defaults = [];

    private function __construct()
    {
        parent::__construct(SpecialType::SIMPLE_VALUE());
    }

    public function getSimpleValueType() : SimpleValueType
    {
        return $this->simpleValueType;
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
        if (SimpleValueType::UNALLOCATED() === $this->simpleValueType
            || SimpleValueType::RESERVED() === $this->simpleValueType
        ) {
            return sprintf('simple(%d)', $this->value);
        }

        return strtolower((string) $this->simpleValueType);
    }

    public function toPhpValue()
    {
        switch ($this->simpleValueType) {
            case SimpleValueType::FALSE():
                return false;

            case SimpleValueType::TRUE():
                return true;

            case SimpleValueType::NULL():
                return null;
        }

        return $this->value;
    }

    public static function ofSimpleValueType(SimpleValueType $simpleValueType)
    {
        $simpleValue = new self();
        $simpleValue->value = $simpleValueType->getValue();
        $simpleValue->simpleValueType = $simpleValueType;
        return $simpleValue;
    }

    public static function ofValue(int $value) : self
    {
        $simpleValue = new self();
        $simpleValue->specialType = $value <= 23 ? SpecialType::SIMPLE_VALUE() : SpecialType::SIMPLE_VALUE_NEXT_BYTE();
        $simpleValue->value = $value;
        $simpleValue->simpleValueType = SimpleValueType::ofByte($value);
        return $simpleValue;
    }

    public static function false() : self
    {
        return self::$defaults['false'] ?? self::$defaults['false'] = self::ofSimpleValueType(SimpleValueType::FALSE());
    }

    public static function true() : self
    {
        return self::$defaults['true'] ?? self::$defaults['true'] = self::ofSimpleValueType(SimpleValueType::TRUE());
    }

    public static function null() : self
    {
        return self::$defaults['null'] ?? self::$defaults['null'] = self::ofSimpleValueType(SimpleValueType::NULL());
    }

    public static function undefined() : self
    {
        return self::$defaults['undefined'] ??
            self::$defaults['undefined'] = self::ofSimpleValueType(SimpleValueType::UNDEFINED());
    }
}
