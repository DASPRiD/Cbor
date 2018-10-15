<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Decoder;

use DASPRiD\Cbor\CborDecoder;
use DASPRiD\Cbor\CborException;
use DASPRiD\Cbor\Data\DataItem;
use DASPRiD\Cbor\Data\SimpleValue;
use DASPRiD\Cbor\Data\SimpleValueType;
use DASPRiD\Cbor\Data\Special;
use DASPRiD\Cbor\Data\SpecialType;
use SplFileObject;

final class SpecialDecoder extends Decoder
{
    /**
     * @var Decoder
     */
    private $halfPrecisionFloatDecoder;

    /**
     * @var Decoder
     */
    private $singlePrecisionFloatDecoder;

    /**
     * @var Decoder
     */
    private $doublePrecisionFloatDecoder;

    public function __construct(SplFileObject $stream, CborDecoder $decoder)
    {
        parent::__construct($stream, $decoder);
        $this->halfPrecisionFloatDecoder = new HalfPrecisionFloatDecoder($stream);
        $this->singlePrecisionFloatDecoder = new SinglePrecisionFloatDecoder($stream);
        $this->doublePrecisionFloatDecoder = new DoublePrecisionFloatDecoder($stream);
    }

    public function decode(int $initialByte) : DataItem
    {
        switch (SpecialType::ofByte($initialByte)) {
            case SpecialType::BREAK():
                return Special::break();

            case SpecialType::SIMPLE_VALUE():
                switch (SimpleValueType::ofByte($initialByte)) {
                    case SimpleValueType::FALSE():
                        return SimpleValue::false();

                    case SimpleValueType::TRUE():
                        return SimpleValue::true();

                    case SimpleValueType::NULL():
                        return SimpleValue::null();

                    case SimpleValueType::UNDEFINED():
                        return SimpleValue::undefined();

                    case SimpleValueType::UNALLOCATED():
                        return SimpleValue::ofValue($initialByte & 31);
                }
                break;

            case SpecialType::IEEE_754_HALF_PRECISION_FLOAT():
                return $this->halfPrecisionFloatDecoder->decode($initialByte);

            case SpecialType::IEEE_754_SINGLE_PRECISION_FLOAT():
                return $this->singlePrecisionFloatDecoder->decode($initialByte);

            case SpecialType::IEEE_754_DOUBLE_PRECISION_FLOAT():
                return $this->doublePrecisionFloatDecoder->decode($initialByte);

            case SpecialType::SIMPLE_VALUE_NEXT_BYTE():
                return SimpleValue::ofValue($this->nextSymbol());
        }

        throw new CborException('Not implemented');
    }
}
