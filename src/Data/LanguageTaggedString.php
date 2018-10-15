<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Data;

final class LanguageTaggedString extends ArrayList
{
    public function __construct(UnicodeString $language, UnicodeString $string)
    {
        parent::__construct();
        $this->setTagFromInt(30);

        $this->add($language);
        $this->add($string);
    }

    public function getLanguage() : UnicodeString
    {
        return $this->getDataItems()[0];
    }

    public function getString() : UnicodeString
    {
        return $this->getDataItems()[1];
    }
}
