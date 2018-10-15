<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Encoder;

use DASPRiD\Cbor\CborEncoder;
use DASPRiD\Cbor\CborException;
use DASPRiD\Cbor\Data\DataItem;
use DASPRiD\Cbor\Data\SimpleValue;
use DASPRiD\Cbor\Data\SimpleValueType;
use DASPRiD\Cbor\Data\Special;
use DASPRiD\Cbor\Data\SpecialType;
use SplFileObject;

final class SpecialEncoder extends Encoder
{
    /**
     * @var Encoder
     */
    private $halfPrecisionFloatEncoder;

    /**
     * @var Encoder
     */
    private $singlePrecisionFloatEncoder;

    /**
     * @var Encoder
     */
    private $doublePrecisionFloatEncoder;

    public function __construct(SplFileObject $stream, CborEncoder $encoder)
    {
        parent::__construct($stream, $encoder);
        $this->halfPrecisionFloatEncoder = new HalfPrecisionFloatEncoder($stream);
        $this->singlePrecisionFloatEncoder = new SinglePrecisionFloatEncoder($stream);
        $this->doublePrecisionFloatEncoder = new DoublePrecisionFloatEncoder($stream);
    }

    public function encode(DataItem $dataItem) : void
    {
        if (! $dataItem instanceof Special) {
            throw new CborException('Wrong data item type');
        }

        switch ($dataItem->getSpecialType()) {
            case SpecialType::BREAK():
                $this->writeByte((7 << 5) | 31);
                break;

            case SpecialType::SIMPLE_VALUE():
                if (! $dataItem instanceof SimpleValue) {
                    throw new CborException('Wrong data item type');
                }

                $simpleValueType = $dataItem->getSimpleValueType();

                switch ($simpleValueType) {
                    case SimpleValueType::FALSE():
                    case SimpleValueType::NULL():
                    case SimpleValueType::TRUE():
                    case SimpleValueType::UNDEFINED():
                        $this->writeByte((7 << 5) | $simpleValueType->getValue());
                        break;

                    case SimpleValueType::UNALLOCATED():
                        $this->writeByte((7 << 5) | $dataItem->getValue());
                        break;
                }
                break;

            case SpecialType::UNALLOCATED():
                throw new CborException('Unallocated special type');

            case SpecialType::IEEE_754_HALF_PRECISION_FLOAT():
                $this->halfPrecisionFloatEncoder->encode($dataItem);
                break;

            case SpecialType::IEEE_754_SINGLE_PRECISION_FLOAT():
                $this->singlePrecisionFloatEncoder->encode($dataItem);
                break;

            case SpecialType::IEEE_754_DOUBLE_PRECISION_FLOAT():
                $this->doublePrecisionFloatEncoder->encode($dataItem);
                break;

            case SpecialType::SIMPLE_VALUE_NEXT_BYTE():
                if (! $dataItem instanceof SimpleValue) {
                    throw new CborException('Wrong data item type');
                }

                $this->writeByte((7 << 5) | 24);
                $this->writeByte($dataItem->getValue());
                break;
        }
    }
}
