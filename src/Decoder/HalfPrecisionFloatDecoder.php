<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Decoder;

use DASPRiD\Cbor\Data\DataItem;
use DASPRiD\Cbor\Data\HalfPrecisionFloat;
use SplFileObject;

final class HalfPrecisionFloatDecoder extends Decoder
{
    public function __construct(SplFileObject $stream)
    {
        $this->stream = $stream;
    }

    public function decode(int $initialByte) : DataItem
    {
        $bits = $this->nextSymbol() << 8 | $this->nextSymbol();
        return new HalfPrecisionFloat(self::toFloat($bits));
    }

    private static function toFloat(int $bits) : float
    {
        $sign = ($bits & 0x8000) >> 15;
        $exponent = ($bits & 0x7c00) >> 10;
        $fraction = $bits & 0x03ff;

        if (0 === $exponent) {
            return (float) ((0 !== $sign ? -1 : 1) * pow(2, -14) * ($fraction / pow(2, 10)));
        }

        if (0x1f === $exponent) {
            return 0 !== $fraction ? NAN : (0 !== $sign ? -1 : 1) * INF;
        }

        return (float) ((0 !== $sign ? -1 : 1) * pow(2, $exponent - 15) * (1 + $fraction / pow(2, 10)));
    }
}
