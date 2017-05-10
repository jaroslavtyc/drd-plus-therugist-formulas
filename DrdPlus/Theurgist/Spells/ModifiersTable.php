<?php
namespace DrdPlus\Theurgist\Spells;

use DrdPlus\Tables\Partials\AbstractFileTable;
use DrdPlus\Tables\Partials\Exceptions\RequiredRowNotFound;
use DrdPlus\Tables\Tables;
use DrdPlus\Theurgist\Codes\FormCode;
use DrdPlus\Theurgist\Codes\FormulaCode;
use DrdPlus\Theurgist\Codes\ModifierCode;
use DrdPlus\Theurgist\Codes\ProfileCode;
use DrdPlus\Theurgist\Codes\SpellTraitCode;
use DrdPlus\Theurgist\Spells\SpellParameters\RealmsAffection;
use DrdPlus\Theurgist\Spells\SpellParameters\Attack;
use DrdPlus\Theurgist\Spells\SpellParameters\CastingRounds;
use DrdPlus\Theurgist\Spells\SpellParameters\Conditions;
use DrdPlus\Theurgist\Spells\SpellParameters\DifficultyChange;
use DrdPlus\Theurgist\Spells\SpellParameters\Grafts;
use DrdPlus\Theurgist\Spells\SpellParameters\Invisibility;
use DrdPlus\Theurgist\Spells\SpellParameters\Points;
use DrdPlus\Theurgist\Spells\SpellParameters\Power;
use DrdPlus\Theurgist\Spells\SpellParameters\Quality;
use DrdPlus\Theurgist\Spells\SpellParameters\Radius;
use DrdPlus\Theurgist\Spells\SpellParameters\Realm;
use DrdPlus\Theurgist\Spells\SpellParameters\Resistance;
use DrdPlus\Theurgist\Spells\SpellParameters\NumberOfSituations;
use DrdPlus\Theurgist\Spells\SpellParameters\EpicenterShift;
use DrdPlus\Theurgist\Spells\SpellParameters\SpellSpeed;
use DrdPlus\Theurgist\Spells\SpellParameters\Threshold;

class ModifiersTable extends AbstractFileTable
{
    use ToFlatArrayTrait;

    /**
     * @var Tables
     */
    private $tables;

    /**
     * @param Tables $tables
     */
    public function __construct(Tables $tables)
    {
        $this->tables = $tables;
    }

    /**
     * @return string
     */
    protected function getDataFileName(): string
    {
        return __DIR__ . '/data/modifiers.csv';
    }

    const REALM = 'realm';
    const REALMS_AFFECTION = 'realms_affection';
    const AFFECTION_TYPE = 'affection_type';
    const CASTING_ROUNDS = 'casting_rounds';
    const DIFFICULTY_CHANGE = 'difficulty_change';
    const RADIUS = 'radius';
    const EPICENTER_SHIFT = 'epicenter_shift';
    const POWER = 'power';
    const ATTACK = 'attack';
    const GRAFTS = 'grafts';
    const SPELL_SPEED = 'spell_speed';
    const POINTS = 'points';
    const INVISIBILITY = 'invisibility';
    const QUALITY = 'quality';
    const CONDITIONS = 'conditions';
    const RESISTANCE = 'resistance';
    const NUMBER_OF_SITUATIONS = 'number_of_situations';
    const THRESHOLD = 'threshold';
    const FORMS = 'forms';
    const SPELL_TRAITS = 'spell_traits';
    const PROFILES = 'profiles';
    const FORMULAS = 'formulas';
    const PARENT_MODIFIERS = 'parent_modifiers';
    const CHILD_MODIFIERS = 'child_modifiers';

    protected function getExpectedDataHeaderNamesToTypes(): array
    {
        return [
            self::REALM => self::POSITIVE_INTEGER,
            self::REALMS_AFFECTION => self::ARRAY,
            self::CASTING_ROUNDS => self::ARRAY,
            self::DIFFICULTY_CHANGE => self::POSITIVE_INTEGER,
            self::RADIUS => self::ARRAY,
            self::EPICENTER_SHIFT => self::ARRAY,
            self::POWER => self::ARRAY,
            self::ATTACK => self::ARRAY,
            self::GRAFTS => self::ARRAY,
            self::SPELL_SPEED => self::ARRAY,
            self::POINTS => self::ARRAY,
            self::INVISIBILITY => self::ARRAY,
            self::QUALITY => self::ARRAY,
            self::CONDITIONS => self::ARRAY,
            self::RESISTANCE => self::ARRAY,
            self::NUMBER_OF_SITUATIONS => self::ARRAY,
            self::THRESHOLD => self::ARRAY,
            self::FORMS => self::ARRAY,
            self::SPELL_TRAITS => self::ARRAY,
            self::PROFILES => self::ARRAY,
            self::FORMULAS => self::ARRAY,
            self::PARENT_MODIFIERS => self::ARRAY,
            self::CHILD_MODIFIERS => self::ARRAY,
        ];
    }

    const MODIFIER = 'modifier';

    protected function getRowsHeader(): array
    {
        return [
            self::MODIFIER,
        ];
    }

    /**
     * @param ModifierCode $modifierCode
     * @return Realm
     */
    public function getRealm(ModifierCode $modifierCode): Realm
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new Realm($this->getValue($modifierCode, self::REALM));
    }

    /**
     * @param ModifierCode $modifierCode
     * @return RealmsAffection|null
     */
    public function getRealmsAffection(ModifierCode $modifierCode)
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        $affectionValues = $this->getValue($modifierCode, self::REALMS_AFFECTION);
        if (count($affectionValues) === 0) {
            return null;
        }

        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new RealmsAffection($affectionValues);
    }

    /**
     * @param ModifierCode $modifierCode
     * @return CastingRounds
     */
    public function getCastingRounds(ModifierCode $modifierCode): CastingRounds
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new CastingRounds($this->getValue($modifierCode, self::CASTING_ROUNDS));
    }

    /**
     * @param ModifierCode $modifierCode
     * @return DifficultyChange
     */
    public function getDifficultyChange(ModifierCode $modifierCode): DifficultyChange
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new DifficultyChange($this->getValue($modifierCode, self::DIFFICULTY_CHANGE));
    }

    /**
     * @param ModifierCode $modifierCode
     * @return Radius|null
     */
    public function getRadius(ModifierCode $modifierCode)
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        $radiusValues = $this->getValue($modifierCode, self::RADIUS);
        if (!$radiusValues) {
            return null;
        }

        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new Radius($radiusValues, $this->tables->getDistanceTable());
    }

    /**
     * @param ModifierCode $modifierCode
     * @return EpicenterShift|null
     */
    public function getEpicenterShift(ModifierCode $modifierCode)
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        $shiftValues = $this->getValue($modifierCode, self::EPICENTER_SHIFT);
        if (!$shiftValues) {
            return null;
        }

        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new EpicenterShift($shiftValues, $this->tables->getDistanceTable());
    }

    /**
     * @param ModifierCode $modifierCode
     * @return Power|null
     */
    public function getPower(ModifierCode $modifierCode)
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        $powerValues = $this->getValue($modifierCode, self::POWER);
        if (!$powerValues) {
            return null;
        }

        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new Power($powerValues);
    }

    /**
     * @param ModifierCode $modifierCode
     * @return Attack|null
     */
    public function getAttack(ModifierCode $modifierCode)
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        $attackValues = $this->getValue($modifierCode, self::ATTACK);
        if (!$attackValues) {
            return null;
        }

        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new Attack($attackValues);
    }

    /**
     * @param ModifierCode $modifierCode
     * @return Grafts|null
     */
    public function getGrafts(ModifierCode $modifierCode)
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        $graftsValues = $this->getValue($modifierCode, self::GRAFTS);
        if (!$graftsValues) {
            return null;
        }

        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new Grafts($graftsValues);
    }

    /**
     * @param ModifierCode $modifierCode
     * @return SpellSpeed|null
     */
    public function getSpellSpeed(ModifierCode $modifierCode)
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        $speedValues = $this->getValue($modifierCode, self::SPELL_SPEED);
        if (!$speedValues) {
            return null;
        }

        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new SpellSpeed($speedValues, $this->tables->getSpeedTable());
    }

    /**
     * @param ModifierCode $modifierCode
     * @return Points|null
     */
    public function getPoints(ModifierCode $modifierCode)
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        $pointsValues = $this->getValue($modifierCode, self::POINTS);
        if (!$pointsValues) {
            return null;
        }

        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new Points($pointsValues);
    }

    /**
     * @param ModifierCode $modifierCode
     * @return Invisibility|null
     */
    public function getInvisibility(ModifierCode $modifierCode)
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        $invisibilityValues = $this->getValue($modifierCode, self::INVISIBILITY);
        if (!$invisibilityValues) {
            return null;
        }

        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new Invisibility($invisibilityValues);
    }

    /**
     * @param ModifierCode $modifierCode
     * @return Quality|null
     */
    public function getQuality(ModifierCode $modifierCode)
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        $qualityValues = $this->getValue($modifierCode, self::QUALITY);
        if (!$qualityValues) {
            return null;
        }

        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new Quality($qualityValues);
    }

    /**
     * @param ModifierCode $modifierCode
     * @return Conditions|null
     */
    public function getConditions(ModifierCode $modifierCode)
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        $conditionsValues = $this->getValue($modifierCode, self::CONDITIONS);
        if (!$conditionsValues) {
            return null;
        }

        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new Conditions($conditionsValues);
    }

    /**
     * @param ModifierCode $modifierCode
     * @return Resistance|null
     */
    public function getResistance(ModifierCode $modifierCode)
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        $resistanceValue = $this->getValue($modifierCode, self::RESISTANCE);
        if (!$resistanceValue) {
            return null;
        }

        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new Resistance($resistanceValue);
    }

    /**
     * @param ModifierCode $modifierCode
     * @return NumberOfSituations|null
     */
    public function getNumberOfSituations(ModifierCode $modifierCode)
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        $numberOfSituationsValue = $this->getValue($modifierCode, self::NUMBER_OF_SITUATIONS);
        if (!$numberOfSituationsValue) {
            return null;
        }

        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new NumberOfSituations($numberOfSituationsValue);
    }

    /**
     * @param ModifierCode $modifierCode
     * @return Threshold|null
     */
    public function getThreshold(ModifierCode $modifierCode)
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        $thresholdValues = $this->getValue($modifierCode, self::THRESHOLD);
        if (!$thresholdValues) {
            return null;
        }

        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new Threshold($thresholdValues);
    }

    /**
     * @param ModifierCode $modifierCode
     * @return array|FormCode[]
     */
    public function getForms(ModifierCode $modifierCode): array
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return array_map(
            function (string $formValue) {
                return FormCode::getIt($formValue);
            },
            $this->getValue($modifierCode, self::FORMS)
        );
    }

    /**
     * @param ModifierCode $modifierCode
     * @return array|SpellTraitCode[]
     */
    public function getSpellTraitCodes(ModifierCode $modifierCode): array
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return array_map(
            function (string $spellTraitValue) {
                /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
                return SpellTraitCode::getIt($spellTraitValue);
            },
            $this->getValue($modifierCode, self::SPELL_TRAITS)
        );
    }

    /**
     * @param ModifierCode $modifierCode
     * @return array|ProfileCode[]
     * @throws \DrdPlus\Theurgist\Spells\Exceptions\UnknownModifierToGetProfilesFor
     */
    public function getProfiles(ModifierCode $modifierCode): array
    {
        try {
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            return array_map(
                function (string $profileValue) {
                    return ProfileCode::getIt($profileValue);
                },
                $this->getValue($modifierCode, self::PROFILES)
            );
        } catch (RequiredRowNotFound $requiredRowNotFound) {
            throw new Exceptions\UnknownModifierToGetProfilesFor("Given modifier code '{$modifierCode}' is unknown");
        }
    }

    /**
     * @param ModifierCode $modifierCode
     * @return array|FormulaCode[]
     * @throws \DrdPlus\Theurgist\Spells\Exceptions\UnknownModifierToGetFormulasFor
     */
    public function getFormulaCodes(ModifierCode $modifierCode): array
    {
        try {
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            return array_map(
                function (string $formulaValue) {
                    return FormulaCode::getIt($formulaValue);
                },
                $this->getValue($modifierCode, self::FORMULAS)
            );
        } catch (RequiredRowNotFound $requiredRowNotFound) {
            throw new Exceptions\UnknownModifierToGetFormulasFor("Given modifier code '{$modifierCode}' is unknown");
        }
    }

    /**
     * @param ModifierCode $modifierCode
     * @return array|ModifierCode[]
     * @throws \DrdPlus\Theurgist\Spells\Exceptions\UnknownModifierToGetParentModifiersFor
     */
    public function getParentModifierCodes(ModifierCode $modifierCode): array
    {
        try {
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            return array_map(
                function (string $modifierValue) {
                    return ModifierCode::getIt($modifierValue);
                },
                $this->getValue($modifierCode, self::PARENT_MODIFIERS)
            );
        } catch (RequiredRowNotFound $requiredRowNotFound) {
            throw new Exceptions\UnknownModifierToGetParentModifiersFor(
                "Given modifier code '{$modifierCode}' is unknown"
            );
        }
    }

    /**
     * @param ModifierCode $modifierCode
     * @return array|ModifierCode[]
     * @throws \DrdPlus\Theurgist\Spells\Exceptions\UnknownModifierToGetChildModifiersFor
     */
    public function getChildModifiers(ModifierCode $modifierCode): array
    {
        try {
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            return array_map(
                function (string $modifierValue) {
                    return ModifierCode::getIt($modifierValue);
                },
                $this->getValue($modifierCode, self::CHILD_MODIFIERS)
            );
        } catch (RequiredRowNotFound $requiredRowNotFound) {
            throw new Exceptions\UnknownModifierToGetChildModifiersFor(
                "Given modifier code '{$modifierCode}' is unknown"
            );
        }
    }

}