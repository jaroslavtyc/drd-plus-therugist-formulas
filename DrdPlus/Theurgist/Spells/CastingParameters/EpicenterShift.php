<?php
namespace DrdPlus\Theurgist\Spells\CastingParameters;

use DrdPlus\Tables\Measurements\Distance\Distance;
use DrdPlus\Tables\Measurements\Distance\DistanceBonus;
use DrdPlus\Tables\Measurements\Distance\DistanceTable;
use DrdPlus\Theurgist\Spells\CastingParameters\Partials\IntegerCastingParameter;

/**
 * @method EpicenterShift getWithAddition($additionValue)
 */
class EpicenterShift extends IntegerCastingParameter
{
    /**
     * @param DistanceTable $distanceTable
     * @return Distance
     */
    public function getDistance(DistanceTable $distanceTable): Distance
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return (new DistanceBonus($this->getValue(), $distanceTable))->getDistance();
    }
}