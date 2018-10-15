<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Encoder;

use DASPRiD\Cbor\CborException;
use DASPRiD\Cbor\Data\DataItem;
use DASPRiD\Cbor\Data\MajorType;
use DASPRiD\Cbor\Data\UnsignedInt;

final class UnsignedIntEncoder extends Encoder
{
    public function encode(DataItem $dataItem) : void
    {
        if (! $dataItem instanceof UnsignedInt) {
            throw new CborException('Wrong data item type');
        }

        $this->encodeTypeAndBigInteger(MajorType::UNSIGNED_INT(), $dataItem->getValue());
    }
}
