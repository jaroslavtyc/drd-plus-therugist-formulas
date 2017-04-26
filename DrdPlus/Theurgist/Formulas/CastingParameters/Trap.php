<?php
namespace DrdPlus\Theurgist\Formulas\CastingParameters;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Theurgist\Formulas\CastingParameters\Partials\IntegerCastingParameter;
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
     * @param array $values with 'trap value', 'trap change by realms' and 'used property'
     * @throws \DrdPlus\Theurgist\Formulas\CastingParameters\Partials\Exceptions\InvalidValueForIntegerCastingParameter
     * @throws \DrdPlus\Theurgist\Formulas\CastingParameters\Partials\Exceptions\MissingValueForAdditionByRealm
     * @throws \DrdPlus\Theurgist\Formulas\CastingParameters\Exceptions\InvalidFormatOfRealmIncrement
     * @throws \DrdPlus\Theurgist\Formulas\CastingParameters\Exceptions\InvalidFormatOfAdditionByRealmValue
     * @throws \DrdPlus\Theurgist\Formulas\CastingParameters\Exceptions\UnexpectedFormatOfAdditionByRealm
     * @throws \DrdPlus\Theurgist\Formulas\CastingParameters\Exceptions\InvalidFormatOfPropertyUsedForTrap
     */
    public function __construct(array $values)
    {
        parent::__construct($values);
        try {
            $this->propertyCode = PropertyCode::getIt($values[2] ?? '');
        } catch (\DrdPlus\Codes\Partials\Exceptions\UnknownValueForCode $unknownValueForCode) {
            throw new Exceptions\InvalidFormatOfPropertyUsedForTrap(
                'Expected valid property code, got '
                . (array_key_exists(2, $values) ? ValueDescriber::describe($values[2]) : 'nothing')
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
     * @param int|float|NumberInterface $value
     * @return Trap
     * @throws \Granam\Integer\Tools\Exceptions\Exception
     */
    public function add($value): Trap
    {
        $value = ToInteger::toInteger($value);
        if ($value === 0) {
            return $this;
        }

        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new static(
            [$this->getValue() + $value, $this->getAdditionByRealms()->getNotation(), $this->getPropertyCode()]
        );
    }

    /**
     * @param int|float|NumberInterface $value
     * @return Trap
     * @throws \Granam\Integer\Tools\Exceptions\Exception
     */
    public function sub($value): Trap
    {
        $value = ToInteger::toInteger($value);
        if ($value === 0) {
            return $this;
        }

        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new static(
            [$this->getValue() - $value, $this->getAdditionByRealms()->getNotation(), $this->getPropertyCode()]
        );
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return "{$this->getValue()} {$this->getPropertyCode()} ({$this->getAdditionByRealms()})";
    }
}