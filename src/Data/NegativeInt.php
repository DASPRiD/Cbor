<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Data;

use Brick\Math\BigInteger;
use DASPRiD\Cbor\CborException;

final class NegativeInt extends IntNumber
{
    /**
     * @var BigInteger
     */
    private static $minusOne;

    public function __construct(BigInteger $value)
    {
        if ($value->isGreaterThanOrEqualTo(0)) {
            throw new CborException('Value must be lower than zero');
        }

        parent::__construct(MajorType::NEGATIVE_INT(), $value);
    }

    public static function minusOne() : BigInteger
    {
        return self::$minusOne ?: self::$minusOne = BigInteger::of(-1);
    }
}
