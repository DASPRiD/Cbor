<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Encoder;

use DASPRiD\Cbor\CborException;
use DASPRiD\Cbor\Data\DataItem;
use DASPRiD\Cbor\Data\MajorType;
use DASPRiD\Cbor\Data\SimpleValue;
use DASPRiD\Cbor\Data\UnicodeString;

final class UnicodeStringEncoder extends Encoder
{
    public function encode(DataItem $dataItem) : void
    {
        if (! $dataItem instanceof UnicodeString) {
            throw new CborException('Wrong data item type');
        }

        $string = $dataItem->getValue();

        if ($dataItem->isChunked()) {
            $this->encodeTypeChunked(MajorType::UNICODE_STRING());

            if (null !== $string) {
                $this->encode(new UnicodeString($string));
            }
        } elseif (null === $string) {
            $this->encoder->encode(SimpleValue::null());
        } else {
            $this->encodeTypeAndLength(MajorType::UNICODE_STRING(), strlen($string));
            $this->writeBytes($string);
        }
    }
}
