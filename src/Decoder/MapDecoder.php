<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Decoder;

use DASPRiD\Cbor\CborException;
use DASPRiD\Cbor\Data\DataItem;
use DASPRiD\Cbor\Data\Map;
use DASPRiD\Cbor\Data\Special;

final class MapDecoder extends Decoder
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
        $map = new Map($this->decoder->getOptions()->shouldRejectDuplicateKeys());

        for ($i = 0; $i < $length; ++$i) {
            $key = $this->decoder->decodeNext();
            $value = $this->decoder->decodeNext();

            if (null === $key || null === $value) {
                throw new CborException('Unexpected end of stream');
            }

            $map->offsetSet($key, $value);
        }

        return $map;
    }

    private function decodeInfinitiveLength() : DataItem
    {
        $map = new Map();
        $map->setChunked(true);

        if (! $this->decoder->getOptions()->shouldAutoDecodeInfinitiveMaps()) {
            return $map;
        }

        while (true) {
            $key = $this->decoder->decodeNext();

            if (null === $key) {
                throw new CborException('Unexpected end of stream');
            }

            if (Special::break()->equals($key)) {
                break;
            }

            $value = $this->decoder->decodeNext();

            if (null === $value) {
                throw new CborException('Unexpected end of stream');
            }

            $map->offsetSet($key, $value);
        }

        return $map;
    }
}
