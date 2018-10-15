<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Data;

use DASPRiD\Enum\AbstractEnum;

/**
 * @method static self SIMPLE_VALUE()
 * @method static self SIMPLE_VALUE_NEXT_BYTE()
 * @method static self IEEE_754_HALF_PRECISION_FLOAT()
 * @method static self IEEE_754_SINGLE_PRECISION_FLOAT()
 * @method static self IEEE_754_DOUBLE_PRECISION_FLOAT()
 * @method static self UNALLOCATED()
 * @method static self BREAK()
 */
final class SpecialType extends AbstractEnum
{
    protected const SIMPLE_VALUE = null;
    protected const SIMPLE_VALUE_NEXT_BYTE = null;
    protected const IEEE_754_HALF_PRECISION_FLOAT = null;
    protected const IEEE_754_SINGLE_PRECISION_FLOAT = null;
    protected const IEEE_754_DOUBLE_PRECISION_FLOAT = null;
    protected const UNALLOCATED = null;
    protected const BREAK = null;

    public static function ofByte(int $byte) : self
    {
        switch ($byte & 31) {
            case 24:
                return self::SIMPLE_VALUE_NEXT_BYTE();

            case 25:
                return self::IEEE_754_HALF_PRECISION_FLOAT();

            case 26:
                return self::IEEE_754_SINGLE_PRECISION_FLOAT();

            case 27:
                return self::IEEE_754_DOUBLE_PRECISION_FLOAT();

            case 28:
            case 29:
            case 30:
                return self::UNALLOCATED();

            case 31:
                return self::BREAK();
        }

        return self::SIMPLE_VALUE();
    }
}
