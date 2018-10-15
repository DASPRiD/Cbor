<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Encoder;

use DASPRiD\Cbor\CborException;
use DASPRiD\Cbor\Data\DataItem;
use DASPRiD\Cbor\Data\MajorType;
use DASPRiD\Cbor\Data\Tag;

final class TagEncoder extends Encoder
{
    public function encode(DataItem $dataItem) : void
    {
        if (! $dataItem instanceof Tag) {
            throw new CborException('Wrong data item type');
        }

        $this->encodeTypeAndLength(MajorType::TAG(), $dataItem->getValue());
    }
}
