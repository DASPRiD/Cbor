<?php
declare(strict_types = 1);

namespace DASPRiD\CborTest;

use Brick\Math\BigInteger;
use DASPRiD\Cbor\CborDecoder;
use DASPRiD\Cbor\CborEncoder;
use PHPUnit\Framework\TestCase;
use SplTempFileObject;

final class AppendixATest extends TestCase
{
    public function appendixAProvider() : array
    {
        $rawData = json_decode(
            file_get_contents(__DIR__ . '/TestAssets/appendix_a.json'),
            true,
            512,
            JSON_BIGINT_AS_STRING
        );

        $rawData = $this->replaceInts($rawData);
        $data = [];

        foreach ($rawData as $datum) {
            $data[] = [
                $datum['cbor'],
                $datum['roundtrip'],
                $datum['diagnostic'] ?? null,
                $datum['decoded'] ?? null
            ];
        }

        return $data;
    }

    private function replaceInts(array $array) : array
    {
        foreach ($array as $key => $value) {
            if (is_int($value)) {
                $array[$key] = BigInteger::of($value);
            } elseif (is_string($value) && preg_match('(^-?[1-9]\d*$)', $value)) {
                $array[$key] = BigInteger::of($value);
            } elseif (is_array($value)) {
                $array[$key] = $this->replaceInts($value);
            }
        }

        return $array;
    }

    /**
     * @dataProvider appendixAProvider
     */
    public function testAppendixA(string $cbor, bool $roundtrip, ?string $diagnostic, $decoded) : void
    {
        $result = CborDecoder::decodeString(base64_decode($cbor));
        $this->assertCount(1, $result);
        $result = $result[0];

        if (null !== $diagnostic) {
            $this->assertSame($diagnostic, (string) $result);
        }

        if (null !== $decoded) {
            $this->assertEquals($decoded, $result->toPhpValue());
        }

        if (! $roundtrip) {
            return;
        }

        $stream = new SplTempFileObject();
        $encoder = new CborEncoder($stream);
        $encoder->encode($result);

        $length = $stream->ftell();
        $stream->rewind();
        $encodedCbor = $stream->fread($length);

        $this->assertSame($cbor, base64_encode($encodedCbor));
    }
}
