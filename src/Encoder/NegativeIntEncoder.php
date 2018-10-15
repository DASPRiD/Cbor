<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Encoder;

use DASPRiD\Cbor\CborException;
use DASPRiD\Cbor\Data\DataItem;
use DASPRiD\Cbor\Data\MajorType;
use DASPRiD\Cbor\Data\NegativeInt;

final class NegativeIntEncoder extends Encoder
{
    public function encode(DataItem $dataItem) : void
    {
        if (! $dataItem instanceof NegativeInt) {
            throw new CborException('Wrong data item type');
        }

        $this->encodeTypeAndBigInteger(
            MajorType::NEGATIVE_INT(),
            NegativeInt::minusOne()->minus($dataItem->getValue())->abs()
        );
    }
}
