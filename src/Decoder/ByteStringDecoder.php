<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Decoder;

use DASPRiD\Cbor\CborException;
use DASPRiD\Cbor\Data\ByteString;
use DASPRiD\Cbor\Data\DataItem;
use DASPRiD\Cbor\Data\MajorType;
use DASPRiD\Cbor\Data\Special;

final class ByteStringDecoder extends Decoder
{
    public function decode(int $initialByte) : DataItem
    {
        $length = $this->getLength($initialByte);

        if (0 === $length) {
            return new ByteString();
        }

        if (self::INFINITY !== $length) {
            return $this->decodeFixedLength($length);
        }

        if ($this->decoder->getOptions()->shouldAutoDecodeInfinitiveByteStrings()) {
            return $this->decodeInfinitiveLength();
        }

        $byteString = new ByteString();
        $byteString->setChunked(true);
        return $byteString;
    }

    private function decodeFixedLength(int $length) : DataItem
    {
        if (0 === $length) {
            return new ByteString('');
        }

        $bytes = $this->stream->fread($length);

        if (false === $bytes || strlen($bytes) !== $length) {
            throw new CborException('Unexpected end of stream');
        }

        return new ByteString($bytes);
    }

    private function decodeInfinitiveLength() : DataItem
    {
        $byteChunks = [];

        while (true) {
            $dataItem = $this->decoder->decodeNext();

            if (null === $dataItem) {
                throw new CborException('Unexpected end of stream');
            }

            $majorType = $dataItem->getMajorType();

            if (Special::break()->equals($dataItem)) {
                break;
            }

            if (MajorType::BYTE_STRING() !== $majorType) {
                throw new CborException('Unexpected major type: ' . $majorType);
            }

            assert($dataItem instanceof ByteString);
            $byteChunks[] = $dataItem->getBytes();
        }

        $byteString = new ByteString(...$byteChunks);
        $byteString->setChunked(true);
        return $byteString;
    }
}
