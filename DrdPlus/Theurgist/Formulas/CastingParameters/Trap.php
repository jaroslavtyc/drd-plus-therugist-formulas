<?php
namespace DrdPlus\Theurgist\Formulas\CastingParameters;

use DrdPlus\Codes\Properties\PropertyCode;

class Trap extends CastingParameter
{
    /**
     * @var PropertyCode
     */
    private $propertyCode;

    /**
     * @param array $values
     * @param PropertyCode $propertyCode
     * @throws \DrdPlus\Theurgist\Formulas\CastingParameters\Exceptions\MissingValueForAdditionByRealm
     * @throws \DrdPlus\Theurgist\Formulas\CastingParameters\Exceptions\InvalidFormatOfRealmsNumber
     * @throws \DrdPlus\Theurgist\Formulas\CastingParameters\Exceptions\InvalidFormatOfAddition
     * @throws \DrdPlus\Theurgist\Formulas\CastingParameters\Exceptions\UnexpectedFormatOfAdditionByRealm
     */
    public function __construct(array $values, PropertyCode $propertyCode)
    {
        parent::__construct($values, 1);
        $this->propertyCode = $propertyCode;
    }

    /**
     * @return PropertyCode
     */
    public function getPropertyCode(): PropertyCode
    {
        return $this->propertyCode;
    }
}