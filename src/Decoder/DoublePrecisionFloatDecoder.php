<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Decoder;

use DASPRiD\Cbor\CborException;
use DASPRiD\Cbor\Data\DataItem;
use DASPRiD\Cbor\Data\DoublePrecisionFloat;
use SplFileObject;

final class DoublePrecisionFloatDecoder extends Decoder
{
    public function __construct(SplFileObject $stream)
    {
        $this->stream = $stream;
    }

    public function decode(int $initialByte) : DataItem
    {
        $data = $this->stream->fread(8);

        if (false === $data || strlen($data) !== 8) {
            throw new CborException('Unexpected end of stream');
        }

        if ("\x7f\xf0\x00\x00\x00\x00\x00\x00" === $data) {
            $value = INF;
        } elseif ("\xff\xf0\x00\x00\x00\x00\x00\x00" === $data) {
            $value = -INF;
        } elseif ("\x7f\xf8\x00\x00\x00\x00\x00\x00" === $data) {
            $value = NAN;
        } else {
            $value = unpack('E', $data)[1];
        }

        return new DoublePrecisionFloat($value);
    }
}
