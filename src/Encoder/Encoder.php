<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Encoder;

use Brick\Math\BigInteger;
use DASPRiD\Cbor\CborEncoder;
use DASPRiD\Cbor\Data\AdditionalInformation;
use DASPRiD\Cbor\Data\ByteString;
use DASPRiD\Cbor\Data\DataItem;
use DASPRiD\Cbor\Data\MajorType;
use DASPRiD\Cbor\Data\Tag;
use SplFileObject;

abstract class Encoder
{
    protected const INFINITY = -1;

    /**
     * @var SplFileObject
     */
    protected $stream;

    /**
     * @var CborEncoder
     */
    protected $encoder;

    public function __construct(SplFileObject $stream, CborEncoder $encoder)
    {
        $this->stream = $stream;
        $this->encoder = $encoder;
    }

    abstract public function encode(DataItem $dataItem) : void;

    final protected function encodeTypeChunked(MajorType $majorType) : void
    {
        $symbol = $majorType->getValue() << 5;
        $symbol |= AdditionalInformation::INDEFINITE()->getValue();

        $this->stream->fwrite(chr($symbol));
    }

    final protected function encodeTypeAndLength(MajorType $majorType, int $length) : void
    {
        $symbol = $majorType->getValue() << 5;

        if ($length <= 23) {
            $this->stream->fwrite(chr($symbol | $length));
        } elseif ($length <= 255) {
            $symbol |= AdditionalInformation::ONE_BYTE()->getValue();
            $this->stream->fwrite(chr($symbol));
            $this->stream->fwrite(chr($length));
        } elseif ($length <= 65535) {
            $symbol |= AdditionalInformation::TWO_BYTES()->getValue();
            $this->stream->fwrite(chr($symbol));
            $this->stream->fwrite(chr($length >> 8));
            $this->stream->fwrite(chr($length & 0xff));
        } elseif ($length <= 4294967295) {
            $symbol |= AdditionalInformation::FOUR_BYTES()->getValue();
            $this->stream->fwrite(chr($symbol));
            $this->stream->fwrite(chr(($length >> 24) & 0xff));
            $this->stream->fwrite(chr(($length >> 16) & 0xff));
            $this->stream->fwrite(chr(($length >> 8) & 0xff));
            $this->stream->fwrite(chr($length & 0xff));
        } else {
            $symbol |= AdditionalInformation::EIGHT_BYTES()->getValue();
            $this->stream->fwrite(chr($symbol));
            $this->stream->fwrite(chr(($length >> 56) & 0xff));
            $this->stream->fwrite(chr(($length >> 48) & 0xff));
            $this->stream->fwrite(chr(($length >> 40) & 0xff));
            $this->stream->fwrite(chr(($length >> 32) & 0xff));
            $this->stream->fwrite(chr(($length >> 24) & 0xff));
            $this->stream->fwrite(chr(($length >> 16) & 0xff));
            $this->stream->fwrite(chr(($length >> 8) & 0xff));
            $this->stream->fwrite(chr($length & 0xff));
        }
    }

    final protected function encodeTypeAndBigInteger(MajorType $majorType, BigInteger $bigInteger) : void
    {
        $symbol = $majorType->getValue() << 5;

        if ($bigInteger->isLessThanOrEqualTo(23)) {
            $this->stream->fwrite(chr($symbol | $bigInteger->toInt()));
        } elseif ($bigInteger->isLessThanOrEqualTo(255)) {
            $symbol |= AdditionalInformation::ONE_BYTE()->getValue();
            $this->stream->fwrite(chr($symbol));
            $value = $bigInteger->toInt();
            $this->stream->fwrite(chr($value));
        } elseif ($bigInteger->isLessThanOrEqualTo(65535)) {
            $symbol |= AdditionalInformation::TWO_BYTES()->getValue();
            $this->stream->fwrite(chr($symbol));
            $value = $bigInteger->toInt();
            $this->stream->fwrite(chr($value >> 8));
            $this->stream->fwrite(chr($value & 0xff));
        } elseif ($bigInteger->isLessThanOrEqualTo('4294967295')) {
            $symbol |= AdditionalInformation::FOUR_BYTES()->getValue();
            $this->stream->fwrite(chr($symbol));
            $this->stream->fwrite($this->encodeBigInteger($bigInteger, 4));
        } elseif ($bigInteger->isLessThanOrEqualTo('18446744073709551615')) {
            $symbol |= AdditionalInformation::EIGHT_BYTES()->getValue();
            $this->stream->fwrite(chr($symbol));
            $this->stream->fwrite($this->encodeBigInteger($bigInteger, 8));
        } else {
            if (MajorType::NEGATIVE_INT() === $majorType) {
                $this->encoder->encode(new Tag(3));
            } else {
                $this->encoder->encode(new Tag(2));
            }

            $length = 0;
            $test = clone $bigInteger;

            do {
                $test = $test->shiftedRight(8);
                ++$length;
            } while (! $test->isZero());

            $this->encoder->encode(new ByteString($this->encodeBigInteger($bigInteger, $length)));
            $this->encodeBigInteger($bigInteger, $length);
        }
    }

    final protected function encodeBigInteger(BigInteger $bigInteger, int $length) : string
    {
        $shift = ($length - 1) * 8;
        $mask = BigInteger::of(0xff);

        $result = '';

        for ($i = 0; $i < $length; ++$i) {
            $result .= chr($bigInteger->shiftedRight($shift)->and($mask)->toInt());
            $shift -= 8;
        }

        return $result;
    }

    final protected function writeByte(int $byte)
    {
        $this->stream->fwrite(chr($byte));
    }

    final protected function writeBytes(string $bytes)
    {
        $this->stream->fwrite($bytes);
    }
}
