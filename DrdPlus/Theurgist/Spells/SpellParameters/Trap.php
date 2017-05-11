<?php
namespace DrdPlus\Theurgist\Spells\SpellParameters;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Theurgist\Spells\SpellParameters\Partials\IntegerCastingParameter;
use Granam\Integer\Tools\ToInteger;
use Granam\Number\NumberInterface;
use Granam\Tools\ValueDescriber;

class Trap extends IntegerCastingParameter
{
    /**
     * @var PropertyCode
     */
    private $propertyCode;

    /**
     * @param array $values [0 => trap value, 1 => trap change by realms, 2=> used property, 3 => current addition]
     * @throws \DrdPlus\Theurgist\Spells\SpellParameters\Partials\Exceptions\InvalidValueForIntegerCastingParameter
     * @throws \DrdPlus\Theurgist\Spells\SpellParameters\Partials\Exceptions\MissingValueForFormulaDifficultyAddition
     * @throws \DrdPlus\Theurgist\Spells\SpellParameters\Exceptions\InvalidFormatOfDifficultyIncrement
     * @throws \DrdPlus\Theurgist\Spells\SpellParameters\Exceptions\InvalidFormatOfAdditionByDifficultyValue
     * @throws \DrdPlus\Theurgist\Spells\SpellParameters\Exceptions\InvalidFormatOfAdditionByDifficultyNotation
     * @throws \DrdPlus\Theurgist\Spells\SpellParameters\Exceptions\InvalidFormatOfPropertyUsedForTrap
     */
    public function __construct(array $values)
    {
        $trapProperty = [];
        if (array_key_exists(2, $values)) { // it SHOULD exists
            $trapProperty[] = $values[2];
            unset($values[2]);
            $values = array_values($values); // reindexing
        }
        parent::__construct($values);
        try {
            $this->propertyCode = PropertyCode::getIt($trapProperty[0] ?? 0);
        } catch (\DrdPlus\Codes\Partials\Exceptions\UnknownValueForCode $unknownValueForCode) {
            throw new Exceptions\InvalidFormatOfPropertyUsedForTrap(
                'Expected valid property code, got '
                . (array_key_exists(0, $trapProperty) ? ValueDescriber::describe($trapProperty[0]) : 'nothing')
            );
        }
    }

    /**
     * @return PropertyCode
     */
    public function getPropertyCode(): PropertyCode
    {
        return $this->propertyCode;
    }

    /**
     * @param int|float|NumberInterface $additionValue
     * @return Trap
     * @throws \Granam\Integer\Tools\Exceptions\Exception
     */
    public function getWithAddition($additionValue): Trap
    {
        $additionValue = ToInteger::toInteger($additionValue);
        if ($additionValue === 0) {
            return $this;
        }

        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new static(
            [
                $this->getValue(),
                $this->getAdditionByDifficulty()->getNotation(),
                $this->getPropertyCode(),
                $additionValue,
            ]
        );
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "{$this->getValue()} {$this->getPropertyCode()} ({$this->getAdditionByDifficulty()})";
    }
}