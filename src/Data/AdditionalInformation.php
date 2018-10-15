<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Data;

use DASPRiD\Enum\AbstractEnum;

/**
 * @method static self DIRECT()
 * @method static self ONE_BYTE()
 * @method static self TWO_BYTES()
 * @method static self FOUR_BYTES()
 * @method static self EIGHT_BYTES()
 * @method static self RESERVED()
 * @method static self INDEFINITE()
 */
final class AdditionalInformation extends AbstractEnum
{
    protected const DIRECT = [0];
    protected const ONE_BYTE = [24];
    protected const TWO_BYTES = [25];
    protected const FOUR_BYTES = [26];
    protected const EIGHT_BYTES = [27];
    protected const RESERVED = [28];
    protected const INDEFINITE = [31];

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
            case 24:
                return self::ONE_BYTE();

            case 25:
                return self::TWO_BYTES();

            case 26:
                return self::FOUR_BYTES();

            case 27:
                return self::EIGHT_BYTES();

            case 28:
            case 29:
            case 30:
                return self::RESERVED();

            case 31:
                return self::INDEFINITE();
        }

        return self::DIRECT();
    }
}
