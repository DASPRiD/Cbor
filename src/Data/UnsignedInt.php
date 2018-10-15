<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Data;

use Brick\Math\BigInteger;
use DASPRiD\Cbor\CborException;

final class UnsignedInt extends IntNumber
{
    public function __construct(BigInteger $value)
    {
        if ($value->isLessThan(0)) {
            throw new CborException('Value must be greater than or equal to zero');
        }

        parent::__construct(MajorType::UNSIGNED_INT(), $value);
    }
}
