<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Decoder;

use DASPRiD\Cbor\Data\DataItem;
use DASPRiD\Cbor\Data\NegativeInt;

final class NegativeIntDecoder extends Decoder
{
    public function decode(int $initialByte) : DataItem
    {
        return new NegativeInt(
            NegativeInt::minusOne()->minus($this->getLengthAsBigInteger($initialByte))
        );
    }
}
