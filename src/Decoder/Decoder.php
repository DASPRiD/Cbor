<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Decoder;

use Brick\Math\BigInteger;
use DASPRiD\Cbor\CborDecoder;
use DASPRiD\Cbor\CborException;
use DASPRiD\Cbor\Data\AdditionalInformation;
use DASPRiD\Cbor\Data\DataItem;
use SplFileObject;

abstract class Decoder
{
    protected const INFINITY = -1;

    /**
     * @var SplFileObject
     */
    protected $stream;

    /**
     * @var CborDecoder
     */
    protected $decoder;

    public function __construct(SplFileObject $stream, CborDecoder $decoder)
    {
        $this->stream = $stream;
        $this->decoder = $decoder;
    }

    abstract public function decode(int $initialByte) : DataItem;

    final protected function nextSymbol() : int
    {
        $symbol = $this->stream->fgetc();

        if (false === $symbol) {
            throw new CborException('Unexpected end of stream');
        }

        return ord($symbol);
    }

    final protected function getLength(int $initialByte) : int
    {
        switch (AdditionalInformation::ofByte($initialByte)) {
            case AdditionalInformation::DIRECT():
                return $initialByte & 31;

            case AdditionalInformation::ONE_BYTE():
                return $this->nextSymbol();

            case AdditionalInformation::TWO_BYTES():
                $value = 0;
                $value |= $this->nextSymbol() << 8;
                $value |= $this->nextSymbol() << 0;
                return $value;

            case AdditionalInformation::FOUR_BYTES():
                $value = 0;
                $value |= $this->nextSymbol() << 24;
                $value |= $this->nextSymbol() << 16;
                $value |= $this->nextSymbol() << 8;
                $value |= $this->nextSymbol() << 0;
                return $value;

            case AdditionalInformation::EIGHT_BYTES():
                $value = 0;
                $value |= $this->nextSymbol() << 56;
                $value |= $this->nextSymbol() << 48;
                $value |= $this->nextSymbol() << 40;
                $value |= $this->nextSymbol() << 32;
                $value |= $this->nextSymbol() << 24;
                $value |= $this->nextSymbol() << 16;
                $value |= $this->nextSymbol() << 8;
                $value |= $this->nextSymbol() << 0;
                return $value;

            case AdditionalInformation::INDEFINITE():
                return self::INFINITY;
        }

        throw new CborException('Reserved additional information');
    }

    final protected function getLengthAsBigInteger(int $initialByte) : BigInteger
    {
        switch (AdditionalInformation::ofByte($initialByte)) {
            case AdditionalInformation::DIRECT():
                return BigInteger::of($initialByte & 31);

            case AdditionalInformation::ONE_BYTE():
                return BigInteger::of($this->nextSymbol());

            case AdditionalInformation::TWO_BYTES():
                $value = 0;
                $value |= $this->nextSymbol() << 8;
                $value |= $this->nextSymbol() << 0;
                return BigInteger::of($value);

            case AdditionalInformation::FOUR_BYTES():
                $value = BigInteger::zero();
                $value = $value->or(BigInteger::of($this->nextSymbol())->shiftedLeft(24));
                $value = $value->or(BigInteger::of($this->nextSymbol())->shiftedLeft(16));
                $value = $value->or(BigInteger::of($this->nextSymbol())->shiftedLeft(8));
                $value = $value->or(BigInteger::of($this->nextSymbol())->shiftedLeft(0));
                return $value;

            case AdditionalInformation::EIGHT_BYTES():
                $value = BigInteger::zero();
                $value = $value->or(BigInteger::of($this->nextSymbol())->shiftedLeft(56));
                $value = $value->or(BigInteger::of($this->nextSymbol())->shiftedLeft(48));
                $value = $value->or(BigInteger::of($this->nextSymbol())->shiftedLeft(40));
                $value = $value->or(BigInteger::of($this->nextSymbol())->shiftedLeft(32));
                $value = $value->or(BigInteger::of($this->nextSymbol())->shiftedLeft(24));
                $value = $value->or(BigInteger::of($this->nextSymbol())->shiftedLeft(16));
                $value = $value->or(BigInteger::of($this->nextSymbol())->shiftedLeft(8));
                $value = $value->or(BigInteger::of($this->nextSymbol())->shiftedLeft(0));
                return $value;

            case AdditionalInformation::INDEFINITE():
                throw new CborException('BigInteger cannot be indefinite');
        }

        throw new CborException('Reserved additional information');
    }
}
