<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Encoder;

use DASPRiD\Cbor\CborEncoder;
use DASPRiD\Cbor\CborException;
use DASPRiD\Cbor\Data\DataItem;
use DASPRiD\Cbor\Data\MajorType;
use DASPRiD\Cbor\Data\Map;
use DASPRiD\Cbor\Data\SimpleValue;
use SplTempFileObject;

final class MapEncoder extends Encoder
{
    public function encode(DataItem $dataItem) : void
    {
        if (! $dataItem instanceof Map) {
            throw new CborException('Wrong data item type');
        }

        if ($dataItem->isChunked()) {
            $this->encodeTypeChunked(MajorType::MAP());
        } else {
            $this->encodeTypeAndLength(MajorType::MAP(), count($dataItem));
        }

        if (0 === count($dataItem)) {
            return;
        }

        if ($dataItem->isChunked()) {
            foreach ($dataItem as $key) {
                $this->encoder->encode($key);
                $this->encoder->encode($dataItem[$key]);
            }

            $this->encoder->encode(SimpleValue::break());
            return;
        }

        $sortedMap = [];
        $stream = new SplTempFileObject();
        $encoder = new CborEncoder($stream);

        foreach ($dataItem as $key) {
            $stream->rewind();
            $encoder->encode($key);
            $length = $stream->ftell();
            $stream->rewind();
            $keyBytes = $stream->fread($length);

            $stream->rewind();
            $encoder->encode($dataItem[$key]);
            $length = $stream->ftell();
            $stream->rewind();
            $valueBytes = $stream->fread($length);

            $sortedMap[$keyBytes] = $valueBytes;
        }

        uksort($sortedMap, function (string $a, string $b) : int {
            $aLength = strlen($a);
            $bLength = strlen($b);

            if ($aLength < $bLength) {
                return -1;
            }

            if ($aLength > $bLength) {
                return 1;
            }

            for ($i = 0; $i < $aLength; ++$i) {
                if ($a[$i] < $b[$i]) {
                    return -1;
                }

                if ($a[$i] > $b[$i]) {
                    return 1;
                }
            }

            return 0;
        });

        foreach ($sortedMap as $key => $value) {
            $this->writeBytes($key);
            $this->writeBytes($value);
        }
    }
}
