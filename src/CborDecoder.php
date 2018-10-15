<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor;

use Brick\Math\BigInteger;
use DASPRiD\Cbor\Data\ArrayList;
use DASPRiD\Cbor\Data\ByteString;
use DASPRiD\Cbor\Data\DataItem;
use DASPRiD\Cbor\Data\LanguageTaggedString;
use DASPRiD\Cbor\Data\MajorType;
use DASPRiD\Cbor\Data\IntNumber;
use DASPRiD\Cbor\Data\NegativeInt;
use DASPRiD\Cbor\Data\RationalNumber;
use DASPRiD\Cbor\Data\Tag;
use DASPRiD\Cbor\Data\UnicodeString;
use DASPRiD\Cbor\Data\UnsignedInt;
use DASPRiD\Cbor\Decoder\ArrayDecoder;
use DASPRiD\Cbor\Decoder\ByteStringDecoder;
use DASPRiD\Cbor\Decoder\Decoder;
use DASPRiD\Cbor\Decoder\MapDecoder;
use DASPRiD\Cbor\Decoder\NegativeIntDecoder;
use DASPRiD\Cbor\Decoder\SpecialDecoder;
use DASPRiD\Cbor\Decoder\TagDecoder;
use DASPRiD\Cbor\Decoder\UnicodeStringDecoder;
use DASPRiD\Cbor\Decoder\UnsignedIntDecoder;
use SplFileObject;
use SplTempFileObject;

final class CborDecoder
{
    /**
     * @var SplFileObject
     */
    private $stream;

    /**
     * @var CborDecoderOptions
     */
    private $options;

    /**
     * @var Decoder
     */
    private $unsignedIntDecoder;

    /**
     * @var Decoder
     */
    private $negativeIntDecoder;

    /**
     * @var Decoder
     */
    private $byteStringDecoder;

    /**
     * @var Decoder
     */
    private $unicodeStringDecoder;

    /**
     * @var Decoder
     */
    private $arrayDecoder;

    /**
     * @var Decoder
     */
    private $mapDecoder;

    /**
     * @var Decoder
     */
    private $tagDecoder;

    /**
     * @var Decoder
     */
    private $specialDecoder;

    public function __construct(SplFileObject $stream, ?CborDecoderOptions $options = null)
    {
        $this->stream = $stream;
        $this->options = $options ?: new CborDecoderOptions();

        $this->unsignedIntDecoder = new UnsignedIntDecoder($this->stream, $this);
        $this->negativeIntDecoder = new NegativeIntDecoder($this->stream, $this);
        $this->byteStringDecoder = new ByteStringDecoder($this->stream, $this);
        $this->unicodeStringDecoder = new UnicodeStringDecoder($this->stream, $this);
        $this->arrayDecoder = new ArrayDecoder($this->stream, $this);
        $this->mapDecoder = new MapDecoder($this->stream, $this);
        $this->tagDecoder = new TagDecoder($this->stream, $this);
        $this->specialDecoder = new SpecialDecoder($this->stream, $this);
    }

    /**
     * @return DataItem[]
     */
    public static function decodeString(string $encoded, ?CborDecoderOptions $options = null) : array
    {
        $stream = new SplTempFileObject();
        $stream->fwrite($encoded);
        $stream->rewind();

        return (new self($stream, $options ?: new CborDecoderOptions()))->decode();
    }

    /**
     * @return DataItem[]
     */
    public function decode() : array
    {
        $dataItems = [];
        $dataItem = $this->decodeNext();

        while (null !== $dataItem) {
            $dataItems[] = $dataItem;
            $dataItem = $this->decodeNext();
        }

        return $dataItems;
    }

    public function decodeNext() : ?DataItem
    {
        $symbol = $this->stream->fgetc();

        if (false === $symbol) {
            return null;
        }

        $symbol = ord($symbol);

        switch (MajorType::ofByte($symbol)) {
            case MajorType::ARRAY():
                return $this->arrayDecoder->decode($symbol);

            case MajorType::BYTE_STRING():
                return $this->byteStringDecoder->decode($symbol);

            case MajorType::MAP():
                return $this->mapDecoder->decode($symbol);

            case MajorType::NEGATIVE_INT():
                return $this->negativeIntDecoder->decode($symbol);

            case MajorType::UNICODE_STRING():
                return $this->unicodeStringDecoder->decode($symbol);

            case MajorType::UNSIGNED_INT():
                return $this->unsignedIntDecoder->decode($symbol);

            case MajorType::SPECIAL():
                return $this->specialDecoder->decode($symbol);

            case MajorType::TAG():
                $tag = $this->tagDecoder->decode($symbol);
                $next = $this->decodeNext();

                if (null === $next) {
                    throw new CborException('Unexpected end of stream: tag without following data item');
                }

                assert($tag instanceof Tag);

                if ($this->options->shouldAutoDecodeBigNumbers() && 2 === $tag->getValue()) {
                    return $this->decodePositiveBigNumber($next);
                }

                if ($this->options->shouldAutoDecodeBigNumbers() && 3 === $tag->getValue()) {
                    return $this->decodeNegativeBigNumber($next);
                }

                if ($this->options->shouldAutoDecodeRationalNumbers() && 30 === $tag->getValue()) {
                    return $this->decodeRationalNumber($next);
                }

                if ($this->options->shouldAutoDecodeLanguageTaggedStrings() && 38 === $tag->getValue()) {
                    return $this->decodeLanguageTaggedString($next);
                }

                $itemToTag = $next;

                while (null !== $itemToTag->getTag()) {
                    $itemToTag = $itemToTag->getTag();
                }

                $itemToTag->setTag($tag);
                return $next;
        }

        throw new CborException('Not implemented major type: ' . $symbol);
    }

    public function getOptions() : CborDecoderOptions
    {
        return $this->options;
    }

    private function decodePositiveBigNumber(DataItem $dataItem) : UnsignedInt
    {
        return new UnsignedInt($this->decodeBigNumber($dataItem));
    }

    private function decodeNegativeBigNumber(DataItem $dataItem) : NegativeInt
    {
        return new NegativeInt(
            NegativeInt::minusOne()->minus($this->decodeBigNumber($dataItem))
        );
    }

    private function decodeBigNumber(DataItem $dataItem) : BigInteger
    {
        if (! $dataItem instanceof ByteString) {
            throw new CborException('Error decoding BigNumber: not a ByteString');
        }

        $bytes = $dataItem->getBytes();
        $length = strlen($bytes);
        $result = BigInteger::zero();

        if (0 === $length) {
            return $result;
        }

        $shift = ($length - 1) * 8;

        for ($i = 0; $i < $length; ++$i) {
            $result = $result->or(BigInteger::of(ord($bytes[$i]))->shiftedLeft($shift));
            $shift -= 8;
        }

        return $result;
    }

    private function decodeRationalNumber(DataItem $dataItem) : RationalNumber
    {
        if (! $dataItem instanceof ArrayList) {
            throw new CborException('Error decoding RationalNumber: not an array');
        }

        $dataItems = $dataItem->getDataItems();

        if (2 !== count($dataItems)) {
            throw new CborException('Error decoding RationalNumber: array size is not 2');
        }

        $numerator = $dataItems[0];

        if (! $numerator instanceof IntNumber) {
            throw new CborException('Error decoding RationalNumber: first data item is not a number');
        }

        $denominator = $dataItems[1];

        if (! $denominator instanceof IntNumber) {
            throw new CborException('Error decoding RationalNumber: second data item is not a number');
        }

        return new RationalNumber($numerator, $denominator);
    }

    private function decodeLanguageTaggedString(DataItem $dataItem) : LanguageTaggedString
    {
        if (! $dataItem instanceof ArrayList) {
            var_dump($dataItem);
            throw new CborException('Error decoding LanguageTaggedString: not an array');
        }

        $dataItems = $dataItem->getDataItems();

        if (2 !== count($dataItems)) {
            throw new CborException('Error decoding LanguageTaggedString: array size is not 2');
        }

        $language = $dataItems[0];

        if (! $language instanceof UnicodeString) {
            throw new CborException('Error decoding LanguageTaggedString: first data item is not an UnicodeString');
        }

        $string = $dataItems[1];

        if (! $string instanceof UnicodeString) {
            throw new CborException('Error decoding LanguageTaggedString: seconddata item is not an UnicodeString');
        }

        return new LanguageTaggedString($language, $string);
    }
}
