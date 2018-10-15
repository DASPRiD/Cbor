<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Decoder;

use DASPRiD\Cbor\CborException;
use DASPRiD\Cbor\Data\DataItem;
use DASPRiD\Cbor\Data\MajorType;
use DASPRiD\Cbor\Data\Special;
use DASPRiD\Cbor\Data\UnicodeString;

final class UnicodeStringDecoder extends Decoder
{
    public function decode(int $initialByte) : DataItem
    {
        $length = $this->getLength($initialByte);

        if (0 === $length) {
            return new UnicodeString('');
        }

        if (self::INFINITY !== $length) {
            return $this->decodeFixedLength($length);
        }

        if ($this->decoder->getOptions()->shouldAutoDecodeInfinitiveUnicodeStrings()) {
            return $this->decodeInfinitiveLength();
        }

        $unicodeString = new UnicodeString(null);
        $unicodeString->setChunked(true);
        return $unicodeString;
    }

    private function decodeFixedLength(int $length) : DataItem
    {
        $value = $this->stream->fread($length);

        if (false === $value || strlen($value) !== $length) {
            throw new CborException('Unexpected end of stream');
        }

        return new UnicodeString($value);
    }

    private function decodeInfinitiveLength() : DataItem
    {
        $value = '';

        while (true) {
            $dataItem = $this->decoder->decodeNext();

            if (null === $dataItem) {
                throw new CborException('Unexpected end of stream');
            }

            $majorType = $dataItem->getMajorType();

            if (Special::break()->equals($dataItem)) {
                break;
            }

            if (MajorType::UNICODE_STRING() !== $majorType) {
                throw new CborException('Unexpected major type: ' . $majorType);
            }

            assert($dataItem instanceof UnicodeString);
            $value .= $dataItem->getValue();
        }

        return new UnicodeString($value);
    }
}
