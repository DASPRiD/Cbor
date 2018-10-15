<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Encoder;

use DASPRiD\Cbor\CborException;
use DASPRiD\Cbor\Data\ArrayList;
use DASPRiD\Cbor\Data\DataItem;
use DASPRiD\Cbor\Data\MajorType;

final class ArrayEncoder extends Encoder
{
    public function encode(DataItem $dataItem) : void
    {
        if (! $dataItem instanceof ArrayList) {
            throw new CborException('Wrong data item type');
        }

        if ($dataItem->isChunked()) {
            $this->encodeTypeChunked(MajorType::ARRAY());
        } else {
            $this->encodeTypeAndLength(MajorType::ARRAY(), count($dataItem));
        }

        foreach ($dataItem as $childDataItem) {
            $this->encoder->encode($childDataItem);
        }
    }
}
