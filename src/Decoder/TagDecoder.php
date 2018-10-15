<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Decoder;

use DASPRiD\Cbor\Data\DataItem;
use DASPRiD\Cbor\Data\Tag;

final class TagDecoder extends Decoder
{
    public function decode(int $initialByte) : DataItem
    {
        return new Tag($this->getLength($initialByte));
    }
}
