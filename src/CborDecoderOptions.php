<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor;

final class CborDecoderOptions
{
    /**
     * @var bool
     */
    private $autoDecodeInfinitiveArrays = true;

    /**
     * @var bool
     */
    private $autoDecodeInfinitiveMaps = true;

    /**
     * @var bool
     */
    private $autoDecodeInfinitiveByteStrings = true;

    /**
     * @var bool
     */
    private $autoDecodeInfinitiveUnicodeStrings = true;

    /**
     * @var bool
     */
    private $autoDecodeBigNumbers = true;

    /**
     * @var bool
     */
    private $autoDecodeRationalNumbers = true;

    /**
     * @var bool
     */
    private $autoDecodeLanguageTaggedStrings = true;

    /**
     * @var bool
     */
    private $rejectDuplicateKeys = false;

    public function setAutoDecodeInfinitiveArrays(bool $autoDecodeInfinitiveArrays)
    {
        $this->autoDecodeInfinitiveArrays = $autoDecodeInfinitiveArrays;
    }

    public function setAutoDecodeInfinitiveMaps(bool $autoDecodeInfinitiveMaps)
    {
        $this->autoDecodeInfinitiveMaps = $autoDecodeInfinitiveMaps;
    }

    public function setAutoDecodeInfinitiveByteStrings(bool $autoDecodeInfinitiveByteStrings)
    {
        $this->autoDecodeInfinitiveByteStrings = $autoDecodeInfinitiveByteStrings;
    }

    public function setAutoDecodeInfinitiveUnicodeStrings(bool $autoDecodeInfinitiveUnicodeStrings)
    {
        $this->autoDecodeInfinitiveUnicodeStrings = $autoDecodeInfinitiveUnicodeStrings;
    }

    public function setAutoDecodeBigNumbers(bool $autoDecodeBigNumbers)
    {
        $this->autoDecodeBigNumbers = $autoDecodeBigNumbers;
    }

    public function setAutoDecodeRationalNumbers(bool $autoDecodeRationalNumbers)
    {
        $this->autoDecodeRationalNumbers = $autoDecodeRationalNumbers;
    }

    public function setAutoDecodeLanguageTaggedStrings(bool $autoDecodeLanguageTaggedStrings)
    {
        $this->autoDecodeLanguageTaggedStrings = $autoDecodeLanguageTaggedStrings;
    }

    public function setRejectDuplicateKeys(bool $rejectDuplicateKeys)
    {
        $this->rejectDuplicateKeys = $rejectDuplicateKeys;
    }

    public function shouldAutoDecodeInfinitiveArrays() : bool
    {
        return $this->autoDecodeInfinitiveArrays;
    }

    public function shouldAutoDecodeInfinitiveMaps() : bool
    {
        return $this->autoDecodeInfinitiveMaps;
    }

    public function shouldAutoDecodeInfinitiveByteStrings() : bool
    {
        return $this->autoDecodeInfinitiveByteStrings;
    }

    public function shouldAutoDecodeInfinitiveUnicodeStrings() : bool
    {
        return $this->autoDecodeInfinitiveUnicodeStrings;
    }

    public function shouldAutoDecodeBigNumbers() : bool
    {
        return $this->autoDecodeBigNumbers;
    }

    public function shouldAutoDecodeRationalNumbers() : bool
    {
        return $this->autoDecodeRationalNumbers;
    }

    public function shouldAutoDecodeLanguageTaggedStrings() : bool
    {
        return $this->autoDecodeLanguageTaggedStrings;
    }

    public function shouldRejectDuplicateKeys() : bool
    {
        return $this->rejectDuplicateKeys;
    }
}
