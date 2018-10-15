<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Data;

final class UnicodeString extends ChunkableDataItem
{
    /**
     * @var string|null
     */
    private $value;

    public function __construct(?string $value)
    {
        parent::__construct(MajorType::UNICODE_STRING());
        $this->value = $value;
    }

    public function getValue() : ?string
    {
        return $this->value;
    }

    public function equals(DataItem $other) : bool
    {
        return parent::equals($other) && $other instanceof self && $other->value === $this->value;
    }

    public function diagnostic() : string
    {
        return null === $this->value ? 'null' : sprintf('"%s"', $this->value);
    }

    public function toPhpValue()
    {
        return $this->value;
    }
}
