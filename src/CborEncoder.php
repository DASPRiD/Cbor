<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor;

use DASPRiD\Cbor\Data\DataItem;
use DASPRiD\Cbor\Data\MajorType;
use DASPRiD\Cbor\Encoder\ArrayEncoder;
use DASPRiD\Cbor\Encoder\ByteStringEncoder;
use DASPRiD\Cbor\Encoder\Encoder;
use DASPRiD\Cbor\Encoder\MapEncoder;
use DASPRiD\Cbor\Encoder\NegativeIntEncoder;
use DASPRiD\Cbor\Encoder\SpecialEncoder;
use DASPRiD\Cbor\Encoder\TagEncoder;
use DASPRiD\Cbor\Encoder\UnicodeStringEncoder;
use DASPRiD\Cbor\Encoder\UnsignedIntEncoder;
use SplFileObject;

final class CborEncoder
{
    /**
     * @var SplFileObject
     */
    private $stream;

    /**
     * @var Encoder
     */
    private $unsignedIntEncoder;

    /**
     * @var Encoder
     */
    private $negativeIntEncoder;

    /**
     * @var Encoder
     */
    private $byteStringEncoder;

    /**
     * @var Encoder
     */
    private $unicodeStringEncoder;

    /**
     * @var Encoder
     */
    private $arrayEncoder;

    /**
     * @var Encoder
     */
    private $mapEncoder;

    /**
     * @var Encoder
     */
    private $tagEncoder;

    /**
     * @var Encoder
     */
    private $specialEncoder;

    public function __construct(SplFileObject $stream)
    {
        $this->stream = $stream;

        $this->unsignedIntEncoder = new UnsignedIntEncoder($this->stream, $this);
        $this->negativeIntEncoder = new NegativeIntEncoder($this->stream, $this);
        $this->byteStringEncoder = new ByteStringEncoder($this->stream, $this);
        $this->unicodeStringEncoder = new UnicodeStringEncoder($this->stream, $this);
        $this->arrayEncoder = new ArrayEncoder($this->stream, $this);
        $this->mapEncoder = new MapEncoder($this->stream, $this);
        $this->tagEncoder = new TagEncoder($this->stream, $this);
        $this->specialEncoder = new SpecialEncoder($this->stream, $this);
    }

    public function encode(DataItem ...$dataItems) : void
    {
        foreach ($dataItems as $dataItem) {
            $this->encodeDataItem($dataItem);
        }
    }

    private function encodeDataItem(DataItem $dataItem) : void
    {
        $tag = $dataItem->getTag();

        if (null !== $tag) {
            $this->tagEncoder->encode($tag);
        }

        switch ($dataItem->getMajorType()) {
            case MajorType::UNSIGNED_INT():
                $this->unsignedIntEncoder->encode($dataItem);
                break;

            case MajorType::NEGATIVE_INT():
                $this->negativeIntEncoder->encode($dataItem);
                break;

            case MajorType::BYTE_STRING():
                $this->byteStringEncoder->encode($dataItem);
                break;

            case MajorType::UNICODE_STRING():
                $this->unicodeStringEncoder->encode($dataItem);
                break;

            case MajorType::ARRAY():
                $this->arrayEncoder->encode($dataItem);
                break;

            case MajorType::MAP():
                $this->mapEncoder->encode($dataItem);
                break;

            case MajorType::SPECIAL():
                $this->specialEncoder->encode($dataItem);
                break;

            case MajorType::TAG():
                $this->tagEncoder->encode($dataItem);
                break;

            default:
                throw new CborException('Unknown major type');
        }
    }
}
