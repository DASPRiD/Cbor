<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Data;

class Special extends DataItem
{
    /**
     * @var SpecialType
     */
    protected $specialType;

    /**
     * @var self|null
     */
    private static $break;

    protected function __construct(SpecialType $specialType)
    {
        parent::__construct(MajorType::SPECIAL());
        $this->specialType = $specialType;
    }

    public static function break() : self
    {
        return self::$break ?: self::$break = new self(SpecialType::BREAK());
    }

    public function getSpecialType() : SpecialType
    {
        return $this->specialType;
    }

    public function equals(DataItem $other) : bool
    {
        return parent::equals($other) && $other instanceof self && $other->specialType === $this->specialType;
    }

    public function diagnostic() : string
    {
        return '';
    }

    public function toPhpValue()
    {
        return null;
    }
}
