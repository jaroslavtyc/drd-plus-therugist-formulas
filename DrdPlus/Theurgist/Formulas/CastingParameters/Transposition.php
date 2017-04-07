<?php
namespace DrdPlus\Theurgist\Formulas\CastingParameters;

use Granam\Integer\PositiveInteger;
use Granam\Integer\Tools\ToInteger;
use Granam\Tools\ValueDescriber;

class Transposition extends CastingParameter implements PositiveInteger
{
    /**
     * @var int
     */
    private $value;

    /**
     * @param array $values
     * @throws \DrdPlus\Theurgist\Formulas\CastingParameters\Exceptions\InvalidValueForTransposition
     * @throws \DrdPlus\Theurgist\Formulas\CastingParameters\Exceptions\MissingValueForAdditionByRealm
     * @throws \DrdPlus\Theurgist\Formulas\CastingParameters\Exceptions\InvalidFormatOfRealmsNumber
     * @throws \DrdPlus\Theurgist\Formulas\CastingParameters\Exceptions\InvalidFormatOfAddition
     * @throws \DrdPlus\Theurgist\Formulas\CastingParameters\Exceptions\UnexpectedFormatOfAdditionByRealm
     */
    public function __construct(array $values)
    {
        try {
            $this->value = ToInteger::toPositiveInteger($values[0] ?? null);
        } catch (\Granam\Integer\Tools\Exceptions\Exception $exception) {
            throw new Exceptions\InvalidValueForTransposition(
                'Expected positive integer for transposition, got '
                . (array_key_exists(0, $values) ? ValueDescriber::describe($values[0], true) : 'nothing')
            );
        }
        parent::__construct($values, 1);
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return "{$this->getValue()}/{$this->getAdditionByRealm()}";
    }

}