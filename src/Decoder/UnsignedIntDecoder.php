<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Decoder;

use DASPRiD\Cbor\Data\DataItem;
use DASPRiD\Cbor\Data\UnsignedInt;

final class UnsignedIntDecoder extends Decoder
{
    public function decode(int $initialByte) : DataItem
    {
        return new UnsignedInt($this->getLengthAsBigInteger($initialByte));
    }
}
