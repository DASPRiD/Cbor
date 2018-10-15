<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Data;

abstract class ChunkableDataItem extends DataItem
{
    /**
     * @var bool
     */
    protected $chunked = false;

    public function isChunked() : bool
    {
        return $this->chunked;
    }

    public function setChunked(bool $chunked)
    {
        $this->chunked = $chunked;
    }
}
