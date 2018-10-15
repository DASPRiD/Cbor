<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Builder;

use DASPRiD\Cbor\Data\SimpleValue;

final class UnicodeStringBuilder extends Builder
{
    public function __construct(Builder $parent)
    {
        parent::__construct($parent);
    }

    public function add(string $string) : self
    {
        $this->getParent()->addChunk(self::convert($string));
        return $this;
    }

    public function end() : Builder
    {
        $this->getParent()->addChunk(SimpleValue::break());
        return $this->getParent();
    }
}
