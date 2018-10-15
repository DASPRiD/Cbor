<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Decoder;

use DASPRiD\Cbor\CborException;
use DASPRiD\Cbor\Data\DataItem;
use DASPRiD\Cbor\Data\SinglePrecisionFloat;
use SplFileObject;

final class SinglePrecisionFloatDecoder extends Decoder
{
    public function __construct(SplFileObject $stream)
    {
        $this->stream = $stream;
    }

    public function decode(int $initialByte) : DataItem
    {
        $data = $this->stream->fread(4);

        if (false === $data || strlen($data) !== 4) {
            throw new CborException('Unexpected end of stream');
        }

        if ("\x7f\x80\x00\x00" === $data) {
            $value = INF;
        } elseif ("\xff\x80\x00\x00" === $data) {
            $value = -INF;
        } elseif ("\x7f\xc0\x00\x00" === $data) {
            $value = NAN;
        } else {
            $value = unpack('G', $data)[1];
        }

        return new SinglePrecisionFloat($value);
    }
}
