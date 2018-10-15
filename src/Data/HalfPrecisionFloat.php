<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Data;

final class HalfPrecisionFloat extends FloatNumber
{
    public function __construct(float $value)
    {
        parent::__construct(SpecialType::IEEE_754_HALF_PRECISION_FLOAT(), $value);
    }
}
