<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Encoder;

use DASPRiD\Cbor\CborException;
use DASPRiD\Cbor\Data\DataItem;
use DASPRiD\Cbor\Data\DoublePrecisionFloat;
use SplFileObject;

final class DoublePrecisionFloatEncoder extends Encoder
{
    public function __construct(SplFileObject $stream)
    {
        $this->stream = $stream;
    }

    public function encode(DataItem $dataItem) : void
    {
        if (! $dataItem instanceof DoublePrecisionFloat) {
            throw new CborException('Wrong data item type');
        }

        $value = $dataItem->getValue();

        $this->writeByte((7 << 5) | 27);
        $this->writeBytes(pack('E', $value));
    }
}
