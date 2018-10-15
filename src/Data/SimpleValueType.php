<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Data;

use DASPRiD\Enum\AbstractEnum;

/**
 * @method static self FALSE()
 * @method static self TRUE()
 * @method static self NULL()
 * @method static self UNDEFINED()
 * @method static self RESERVED()
 * @method static self UNALLOCATED()
 */
final class SimpleValueType extends AbstractEnum
{
    protected const FALSE = [20];
    protected const TRUE = [21];
    protected const NULL = [22];
    protected const UNDEFINED = [23];
    protected const RESERVED = [0];
    protected const UNALLOCATED = [0];

    /**
     * @var int
     */
    private $value;

    protected function __construct(int $value)
    {
        $this->value = $value;
    }

    public function getValue() : int
    {
        return $this->value;
    }

    public static function ofByte(int $byte) : self
    {
        switch ($byte & 31) {
            case 20:
                return self::FALSE();

            case 21:
                return self::TRUE();

            case 22:
                return self::NULL();

            case 23:
                return self::UNDEFINED();

            case 24:
            case 25:
            case 26:
            case 27:
            case 28:
            case 29:
            case 30:
            case 31:
                return self::RESERVED();
        }

        return self::UNALLOCATED();
    }
}
