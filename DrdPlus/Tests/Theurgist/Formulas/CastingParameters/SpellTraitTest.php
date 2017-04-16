<?php
namespace DrdPlus\Tests\Theurgist\Formulas\CastingParameters;

use DrdPlus\Theurgist\Codes\SpellTraitCode;
use DrdPlus\Theurgist\Formulas\CastingParameters\SpellTrait;
use Granam\Tests\Tools\TestWithMockery;

class SpellTraitTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_use_it()
    {
        $implicitSpellTrait = new SpellTrait(SpellTraitCode::ACTIVE);
        self::assertSame(SpellTraitCode::getIt(SpellTraitCode::ACTIVE), $implicitSpellTrait->getSpellTraitCode());
        self::assertSame(1, $implicitSpellTrait->getDifficultyChange());

        $explicitSpellTrait = new SpellTrait(SpellTraitCode::BIDIRECTIONAL . '=123');
        self::assertSame(SpellTraitCode::getIt(SpellTraitCode::BIDIRECTIONAL), $explicitSpellTrait->getSpellTraitCode());
        self::assertSame(123, $explicitSpellTrait->getDifficultyChange());

        $negativeSpellTrait = new SpellTrait(SpellTraitCode::NATURE_CHANGE . '=-456');
        self::assertSame(SpellTraitCode::getIt(SpellTraitCode::NATURE_CHANGE), $negativeSpellTrait->getSpellTraitCode());
        self::assertSame(-456, $negativeSpellTrait->getDifficultyChange());
    }

    /**
     * @test
     * @expectedException \DrdPlus\Theurgist\Formulas\CastingParameters\Exceptions\InvalidValueForSpellTraitDifficultyChange
     * @expectedExceptionMessageRegExp ~impossible~
     */
    public function I_can_not_create_it_with_non_number_difficulty_change()
    {
        new SpellTrait(SpellTraitCode::ODORLESS . '=impossible');
    }

    /**
     * @test
     * @expectedException \DrdPlus\Theurgist\Formulas\CastingParameters\Exceptions\UnexpectedFormatOfSpellTrait
     * @expectedExceptionMessageRegExp ~deformation=14=-78~
     */
    public function I_can_not_crete_it_from_string_with_too_many_parts()
    {
        new SpellTrait(SpellTraitCode::DEFORMATION . '=14=-78');
    }
}