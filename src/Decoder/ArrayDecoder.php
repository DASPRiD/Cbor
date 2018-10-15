<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Decoder;

use DASPRiD\Cbor\CborException;
use DASPRiD\Cbor\Data\ArrayList;
use DASPRiD\Cbor\Data\DataItem;
use DASPRiD\Cbor\Data\Special;

final class ArrayDecoder extends Decoder
{
    public function decode(int $initialByte) : DataItem
    {
        $length = $this->getLength($initialByte);

        if (self::INFINITY !== $length) {
            return $this->decodeFixedLength($length);
        }

        return $this->decodeInfinitiveLength();
    }

    private function decodeFixedLength(int $length) : DataItem
    {
        $array = new ArrayList();

        for ($i = 0; $i < $length; ++$i) {
            $value = $this->decoder->decodeNext();

            if (null === $value) {
                throw new CborException('Unexpected end of stream');
            }

            $array->add($value);
        }

        return $array;
    }

    private function decodeInfinitiveLength() : DataItem
    {
        $array = new ArrayList();
        $array->setChunked(true);

        if (! $this->decoder->getOptions()->shouldAutoDecodeInfinitiveArrays()) {
            return $array;
        }

        while (true) {
            $value = $this->decoder->decodeNext();

            if (null === $value) {
                throw new CborException('Unexpected end of stream');
            }

            if (Special::break()->equals($value)) {
                break;
            }

            $array->add($value);
        }

        return $array;
    }
}
