<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Data;

abstract class DataItem
{
    /**
     * @var MajorType
     */
    private $majorType;

    /**
     * @var Tag|null
     */
    private $tag;

    protected function __construct(MajorType $majorType)
    {
        $this->majorType = $majorType;
    }

    public function getMajorType() : MajorType
    {
        return $this->majorType;
    }

    public function setTagFromInt(int $tag) : void
    {
        $this->setTag(new Tag($tag));
    }

    public function setTag(Tag $tag) : void
    {
        $this->tag = $tag;
    }

    public function removeTag() : void
    {
        $this->tag = null;
    }

    public function getTag() : ?Tag
    {
        return $this->tag;
    }

    public function equals(DataItem $other) : bool
    {
        if (null !== $this->tag xor null !== $other->tag) {
            return false;
        }

        if (null !== $this->tag) {
            return $this->tag->equals($other->tag) && $this->majorType === $other->majorType;
        }

        return null === $other->tag && $this->majorType === $other->majorType;
    }

    abstract public function diagnostic() : string;

    abstract public function toPhpValue();

    public function __toString() : string
    {
        $diagnostic = $this->diagnostic();

        if (null === $this->tag) {
            return $diagnostic;
        }

        if ('(' === substr($diagnostic, 0, 1)) {
            return sprintf('%d%s', $this->tag, $diagnostic);
        }

        return sprintf('%d(%s)', $this->tag->getValue(), $diagnostic);
    }
}
