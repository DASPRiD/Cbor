<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Data;

use DASPRiD\Enum\AbstractEnum;

/**
 * @method static self INVALID()
 * @method static self UNSIGNED_INT()
 * @method static self NEGATIVE_INT()
 * @method static self BYTE_STRING()
 * @method static self UNICODE_STRING()
 * @method static self ARRAY()
 * @method static self MAP()
 * @method static self TAG()
 * @method static self SPECIAL()
 */
final class MajorType extends AbstractEnum
{
    protected const INVALID = [-1];
    protected const UNSIGNED_INT = [0];
    protected const NEGATIVE_INT = [1];
    protected const BYTE_STRING = [2];
    protected const UNICODE_STRING = [3];
    protected const ARRAY = [4];
    protected const MAP = [5];
    protected const TAG = [6];
    protected const SPECIAL = [7];

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
        switch ($byte >> 5) {
            case 0:
                return self::UNSIGNED_INT();

            case 1:
                return self::NEGATIVE_INT();

            case 2:
                return self::BYTE_STRING();

            case 3:
                return self::UNICODE_STRING();

            case 4:
                return self::ARRAY();

            case 5:
                return self::MAP();

            case 6:
                return self::TAG();

            case 7:
                return self::SPECIAL();
        }

        return self::INVALID();
    }
}
