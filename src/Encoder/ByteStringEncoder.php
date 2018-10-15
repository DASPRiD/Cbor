<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Encoder;

use DASPRiD\Cbor\CborException;
use DASPRiD\Cbor\Data\ByteString;
use DASPRiD\Cbor\Data\DataItem;
use DASPRiD\Cbor\Data\MajorType;
use DASPRiD\Cbor\Data\SimpleValue;

final class ByteStringEncoder extends Encoder
{
    public function encode(DataItem $dataItem) : void
    {
        if (! $dataItem instanceof ByteString) {
            throw new CborException('Wrong data item type');
        }

        $bytes = $dataItem->getBytes();

        if ($dataItem->isChunked()) {
            $this->encodeTypeChunked(MajorType::BYTE_STRING());

            if (null !== $bytes) {
                $this->encode(new ByteString($bytes));
            }
        } elseif (null === $bytes) {
            $this->encoder->encode(SimpleValue::null());
        } else {
            $this->encodeTypeAndLength(MajorType::BYTE_STRING(), strlen($bytes));
            $this->writeBytes($bytes);
        }
    }
}
