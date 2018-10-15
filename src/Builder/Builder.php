<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Builder;

use Brick\Math\BigInteger;
use DASPRiD\Cbor\CborException;
use DASPRiD\Cbor\Data\ByteString;
use DASPRiD\Cbor\Data\DataItem;
use DASPRiD\Cbor\Data\DoublePrecisionFloat;
use DASPRiD\Cbor\Data\HalfPrecisionFloat;
use DASPRiD\Cbor\Data\NegativeInt;
use DASPRiD\Cbor\Data\SimpleValue;
use DASPRiD\Cbor\Data\SinglePrecisionFloat;
use DASPRiD\Cbor\Data\Tag;
use DASPRiD\Cbor\Data\UnicodeString;
use DASPRiD\Cbor\Data\UnsignedInt;
use DASPRiD\Cbor\Decoder\HalfPrecisionFloatDecoder;
use DASPRiD\Cbor\Encoder\HalfPrecisionFloatEncoder;
use DASPRiD\Cbor\Encoder\SinglePrecisionFloatEncoder;
use SplTempFileObject;

abstract class Builder
{
    /**
     * @var Builder|null
     */
    private $parent;

    public function __construct(?Builder $parent)
    {
        $this->parent = $parent;
    }

    public function getParent() : ?Builder
    {
        return $this->parent;
    }

    protected function addChunk(DataItem $dataItem) : void
    {
        throw new CborException('Illegal state');
    }

    final protected static function convert($value) : DataItem
    {
        if (is_string($value)) {
            return self::convertString($value);
        } elseif (is_int($value)) {
            return self::convertInt($value);
        } elseif (is_bool($value)) {
            return self::convertBool($value);
        } elseif (is_float($value)) {
            return self::convertFloat($value);
        } elseif ($value instanceof BigInteger) {
            return self::convertBigInteger($value);
        }

        throw new CborException('Non-convertible type: ' . is_object($value) ? get_class($value) : gettype($value));
    }

    final protected static function convertBytes(string $value) : DataItem
    {
        return new ByteString($value);
    }

    final protected static function convertTag(int $value) : DataItem
    {
        return new Tag($value);
    }

    private static function convertInt(int $value) : DataItem
    {
        return self::convertBigInteger(BigInteger::of($value));
    }

    private static function convertBigInteger(BigInteger $value) : DataItem
    {
        if ($value->isGreaterThanOrEqualTo(0)) {
            return new UnsignedInt($value);
        }

        return new NegativeInt($value);
    }

    private static function convertFloat(float $value) : DataItem
    {
        $stream = new SplTempFileObject();
        $halfPrecisionFloatEncoder = new HalfPrecisionFloatEncoder($stream);
        $halfPrecisionFloatEncoder->encode(new HalfPrecisionFloat($value));

        $stream->rewind();
        $halfPrecisionFloatDecoder = new HalfPrecisionFloatDecoder($stream);
        $halfPrecisionFloat = $halfPrecisionFloatDecoder->decode(0);
        assert($halfPrecisionFloat instanceof HalfPrecisionFloat);

        if ($value === $halfPrecisionFloat->getValue()) {
            return new HalfPrecisionFloat($value);
        }

        $stream = new SplTempFileObject();
        $singlePrecisionFloatEncoder = new SinglePrecisionFloatEncoder($stream);
        $singlePrecisionFloatEncoder->encode(new SinglePrecisionFloat($value));

        $stream->rewind();
        $singlePrecisionFloatDecoder = new HalfPrecisionFloatDecoder($stream);
        $singlePrecisionFloat = $singlePrecisionFloatDecoder->decode(0);
        assert($singlePrecisionFloat instanceof SinglePrecisionFloat);

        if ($value === $singlePrecisionFloat->getValue()) {
            return new SinglePrecisionFloat($value);
        }

        return new DoublePrecisionFloat($value);
    }

    private static function convertBool(bool $value) : DataItem
    {
        if ($value) {
            return SimpleValue::true();
        }

        return SimpleValue::false();
    }

    private static function convertString(string $value) : DataItem
    {
        return new UnicodeString($value);
    }
}
