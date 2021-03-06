<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Theurgist\Spells;

use DrdPlus\Codes\Theurgist\SpellTraitCode;
use DrdPlus\Theurgist\Spells\SpellParameters\AdditionByDifficulty;
use DrdPlus\Theurgist\Spells\SpellParameters\DifficultyChange;
use DrdPlus\Theurgist\Spells\SpellParameters\Trap;
use DrdPlus\Theurgist\Spells\SpellTrait;
use DrdPlus\Theurgist\Spells\SpellTraitsTable;
use Granam\Tests\Tools\TestWithMockery;
use Mockery\MockInterface;

class SpellTraitTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_use_it()
    {
        $active = new SpellTrait(
            SpellTraitCode::getIt(SpellTraitCode::ACTIVE),
            $this->createSpellTraitsTableShell()
        );
        self::assertSame(SpellTraitCode::getIt(SpellTraitCode::ACTIVE), $active->getSpellTraitCode());
        self::assertSame('active', (string)$active);
    }

    /**
     * @return \Mockery\MockInterface|SpellTraitsTable
     */
    private function createSpellTraitsTableShell()
    {
        return $this->mockery(SpellTraitsTable::class);
    }

    /**
     * @test
     */
    public function I_can_get_trap()
    {
        foreach (SpellTraitCode::getPossibleValues() as $spellTraitValue) {
            $spellTraitCode = SpellTraitCode::getIt($spellTraitValue);
            $spellTraitsTable = $this->createSpellTraitsTable($spellTraitCode, $trap = $this->createTrap(0));
            $spellTraitWithoutTrapChange = new SpellTrait($spellTraitCode, $spellTraitsTable, 0);
            self::assertSame($trap, $spellTraitWithoutTrapChange->getBaseTrap());
            self::assertSame($trap, $spellTraitWithoutTrapChange->getCurrentTrap());

            $spellTraitsTable = $this->createSpellTraitsTable(
                $spellTraitCode,
                $baseTrap = $this->createTrap(2 /* 10 - 8 */, 8, $changedTrap = $this->createTrapShell())
            );
            $spellTraitWithTrapChange = new SpellTrait($spellTraitCode, $spellTraitsTable, 10);
            self::assertSame($baseTrap, $spellTraitWithTrapChange->getBaseTrap());
            self::assertEquals($changedTrap, $spellTraitWithTrapChange->getCurrentTrap());
        }
    }

    /**
     * @param SpellTraitCode $spellTraitCode
     * @param Trap $trap
     * @return \Mockery\MockInterface|SpellTraitsTable
     */
    private function createSpellTraitsTable(SpellTraitCode $spellTraitCode, Trap $trap = null)
    {
        $spellTraitsTable = $this->mockery(SpellTraitsTable::class);
        $spellTraitsTable->shouldReceive('getTrap')
            ->with($spellTraitCode)
            ->andReturn($trap);

        return $spellTraitsTable;
    }

    /**
     * @param int $expectedTrapChange
     * @param int $trapDefaultValue
     * @param Trap|null $changedTrap
     * @return \Mockery\MockInterface|Trap
     */
    private function createTrap(int $expectedTrapChange, int $trapDefaultValue = 0, Trap $changedTrap = null)
    {
        $trap = $this->mockery(Trap::class);
        $trap->shouldReceive('getDefaultValue')
            ->andReturn($trapDefaultValue);
        $trap->shouldReceive('getWithAddition')
            ->with($expectedTrapChange)
            ->andReturn($changedTrap ?? $trap);

        return $trap;
    }

    /**
     * @return \Mockery\MockInterface|Trap
     */
    private function createTrapShell()
    {
        return $this->mockery(Trap::class);
    }

    /**
     * @test
     */
    public function I_can_get_difficulty_change_with_trap_reflected()
    {
        $spellTraitCode = SpellTraitCode::getIt(SpellTraitCode::INVISIBLE);
        $spellTraitsTable = $this->createSpellTraitsTable(
            $spellTraitCode,
            null // no trap
        );
        $spellTraitWithoutTrapChange = new SpellTrait($spellTraitCode, $spellTraitsTable);
        $this->addDifficultyChangeGetter($spellTraitsTable, $spellTraitCode, $difficultyChange = new DifficultyChange(345));
        self::assertSame($difficultyChange, $spellTraitWithoutTrapChange->getDifficultyChange());

        $spellTraitsTable = $this->createSpellTraitsTable(
            $spellTraitCode,
            $trap = $this->createTrap(111 /* 345 - 234 */, 234, $currentTrap = $this->createTrap(0))
        );
        $spellTraitWithTrapChange = new SpellTrait($spellTraitCode, $spellTraitsTable, 345);
        $this->addDifficultyChangeGetter($spellTraitsTable, $spellTraitCode, $difficultyChange = new DifficultyChange(567));
        $this->addAdditionByDifficultyGetter($currentTrap, 789);
        self::assertEquals($difficultyChange->add(789), $spellTraitWithTrapChange->getDifficultyChange());
    }

    private function addDifficultyChangeGetter(
        MockInterface $spellTraitsTable,
        SpellTraitCode $expectedSpellTraitCode,
        DifficultyChange $difficultyChange
    )
    {
        $spellTraitsTable->shouldReceive('getDifficultyChange')
            ->with($expectedSpellTraitCode)
            ->andReturn($difficultyChange);
    }

    private function addAdditionByDifficultyGetter(MockInterface $trap, int $currentDifficultyIncrement)
    {
        $trap->shouldReceive('getAdditionByDifficulty')
            ->andReturn($additionByDifficulty = $this->mockery(AdditionByDifficulty::class));
        $additionByDifficulty->shouldReceive('getCurrentDifficultyIncrement')
            ->andReturn($currentDifficultyIncrement);
    }

    /**
     * @test
     * @expectedException \DrdPlus\Theurgist\Spells\Exceptions\CanNotChangeNotExistingTrap
     * @expectedExceptionMessageRegExp ~345~
     */
    public function I_can_not_create_spell_trait_without_trap_with_trap_change()
    {
        $spellTraitCode = SpellTraitCode::getIt(SpellTraitCode::INVISIBLE);
        $spellTraitsTable = $this->createSpellTraitsTable(
            $spellTraitCode,
            null // no trap
        );
        new SpellTrait($spellTraitCode, $spellTraitsTable, 345);
    }
}