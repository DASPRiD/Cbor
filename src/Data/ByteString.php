<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Data;

final class ByteString extends ChunkableDataItem
{
    private $byteChunks = [];

    public function __construct(string ...$byteChunks)
    {
        parent::__construct(MajorType::BYTE_STRING());
        $this->byteChunks = $byteChunks;
    }

    public function getBytes() : ?string
    {
        if ($this->chunked && empty($this->byteChunks)) {
            return null;
        }

        return implode('', $this->byteChunks);
    }

    public function equals(DataItem $other) : bool
    {
        return parent::equals($other) && $other instanceof self && $other->byteChunks === $this->byteChunks;
    }

    public function diagnostic() : string
    {
        if (! $this->chunked) {
            return sprintf("h'%s'", bin2hex(implode('', $this->byteChunks)));
        }

        if (empty($this->byteChunks)) {
            return '(_ )';
        }

        return sprintf(
            "(_ h'%s')",
            implode("', h'", array_map('bin2hex', $this->byteChunks))
        );
    }

    public function toPhpValue()
    {
        if ($this->chunked && empty($this->byteChunks)) {
            return null;
        }

        return implode('', $this->byteChunks);
    }
}
