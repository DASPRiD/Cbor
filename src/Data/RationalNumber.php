<?php
declare(strict_types = 1);

namespace DASPRiD\Cbor\Data;

use DASPRiD\Cbor\CborException;

final class RationalNumber extends ArrayList
{
    public function __construct(IntNumber $numerator, IntNumber $denominator)
    {
        parent::__construct();
        $this->setTagFromInt(30);

        if (0 === $denominator->getValue()) {
            throw new CborException('Denominator is zero');
        }

        $this->add($numerator);
        $this->add($denominator);
    }

    public function getNumerator() : IntNumber
    {
        return $this->getDataItems()[0];
    }

    public function getDenominator() : IntNumber
    {
        return $this->getDataItems()[1];
    }
}
