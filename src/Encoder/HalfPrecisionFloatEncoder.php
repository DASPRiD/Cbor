<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Encoder;

use DASPRiD\Cbor\CborException;
use DASPRiD\Cbor\Data\DataItem;
use DASPRiD\Cbor\Data\HalfPrecisionFloat;
use SplFileObject;

final class HalfPrecisionFloatEncoder extends Encoder
{
    /**
     * @var int[]
     */
    private static $baseTable;

    /**
     * @var int[]
     */
    private static $shiftTable;

    public function __construct(SplFileObject $stream)
    {
        $this->stream = $stream;
    }

    public function encode(DataItem $dataItem) : void
    {
        if (! $dataItem instanceof HalfPrecisionFloat) {
            throw new CborException('Wrong data item type');
        }

        $value = $dataItem->getValue();

        $this->writeByte((7 << 5) | 25);
        $this->writeBytes(self::fromFloat($value));
    }

    /**
     * @see http://www.fox-toolkit.org/ftp/fasthalffloatconversion.pdf
     */
    private static function fromFloat(float $value) : string
    {
        self::generateTables();

        $bits = unpack('N', pack('G', $value))[1];
        $index = ($bits >> 23) & 0x1ff;

        return pack(
            'n',
            self::$baseTable[$index] + (($bits & 0x007fffff) >> self::$shiftTable[$index])
        );
    }

    private static function generateTables() : void
    {
        if (null !== self::$baseTable && null !== self::$shiftTable) {
            return;
        }

        for ($i = 0; $i < 256; ++$i) {
            $e = $i - 127;

            if ($e < -24) {
                // Very small numbers map to zero
                self::$baseTable[$i | 0x000] = 0x0000;
                self::$baseTable[$i | 0x100] = 0x8000;
                self::$shiftTable[$i | 0x000] = 24;
                self::$shiftTable[$i | 0x100] = 24;
            } elseif ($e < -14) {
                // Small numbers map to denorms
                self::$baseTable[$i | 0x000] = (0x0400 >> (-$e - 14));
                self::$baseTable[$i | 0x100] = (0x0400 >> (-$e - 14)) | 0x8000;
                self::$shiftTable[$i | 0x000] = -$e - 1;
                self::$shiftTable[$i | 0x100] = -$e - 1;
            } elseif ($e <= 15) {
                // Normal numbers just lose precision
                self::$baseTable[$i | 0x000] = (($e + 15) << 10);
                self::$baseTable[$i | 0x100] = (($e + 15) << 10) | 0x8000;
                self::$shiftTable[$i | 0x000] = 13;
                self::$shiftTable[$i | 0x100] = 13;
            } elseif ($e < 128) {
                // Large numbers map to infinity
                self::$baseTable[$i | 0x000] = 0x7c00;
                self::$baseTable[$i | 0x100] = 0xfc00;
                self::$shiftTable[$i | 0x000] = 24;
                self::$shiftTable[$i | 0x100] = 24;
            } else {
                // Infinity and NaN's stay infinity and NaN's
                self::$baseTable[$i | 0x000] = 0x7c00;
                self::$baseTable[$i | 0x100] = 0xfc00;
                self::$shiftTable[$i | 0x000] = 13;
                self::$shiftTable[$i | 0x100] = 13;
            }
        }
    }
}
