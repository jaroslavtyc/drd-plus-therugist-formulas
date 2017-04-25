<?php
namespace DrdPlus\Tests\Theurgist\Formulas;

use DrdPlus\Tables\Partials\AbstractTable;
use DrdPlus\Tables\Table;
use DrdPlus\Tables\Tables;
use DrdPlus\Theurgist\Codes\AbstractTheurgistCode;
use DrdPlus\Theurgist\Formulas\CastingParameters\Attack;
use DrdPlus\Theurgist\Formulas\ModifiersTable;
use Granam\String\StringTools;
use Granam\Tests\Tools\TestWithMockery;

abstract class AbstractTheurgistTableTest extends TestWithMockery
{
    /**
     * @param string $profile
     * @return string
     */
    protected function reverseProfileGender(string $profile): string
    {
        $oppositeProfile = str_replace('venus', 'mars', $profile, $countOfReplaced);
        if ($countOfReplaced === 1) {
            return $oppositeProfile;
        }
        $oppositeProfile = str_replace('mars', 'venus', $profile, $countOfReplaced);
        self::assertSame(1, $countOfReplaced);

        return $oppositeProfile;
    }

    /**
     * @param AbstractTable $table
     * @param string $formulaName
     * @param string $parameterName
     * @return mixed
     */
    protected function getValueFromTable(AbstractTable $table, string $formulaName, string $parameterName)
    {
        return $table->getIndexedValues()[$formulaName][$parameterName];
    }

    /**
     * @param string $mandatoryParameter
     * @param string|AbstractTheurgistCode $codeClass
     */
    protected function I_can_get_mandatory_parameter(string $mandatoryParameter, string $codeClass)
    {
        $getObligatoryParameter = StringTools::assembleGetterForName($mandatoryParameter);
        $parameterClass = $this->assembleParameterClassName($mandatoryParameter);
        $sutClass = self::getSutClass();
        $sut = new $sutClass(Tables::getIt(), $this->createModifiersTable());
        $tableArgument = $this->findOutTableArgument($parameterClass);
        foreach ($codeClass::getPossibleValues() as $modifierCode) {
            $expectedParameterValue = $this->getValueFromTable($sut, $modifierCode, $mandatoryParameter);
            if ($tableArgument) {
                $parameterObject = $sut->$getObligatoryParameter($codeClass::getIt($modifierCode), $tableArgument);
                $expectedParameterObject = new $parameterClass($expectedParameterValue, $tableArgument);
            } else {
                $parameterObject = $sut->$getObligatoryParameter($codeClass::getIt($modifierCode));
                $expectedParameterObject = new $parameterClass($expectedParameterValue);
            }
            self::assertEquals($expectedParameterObject, $parameterObject);
        }
    }

    /**
     * @return \Mockery\MockInterface|ModifiersTable
     */
    private function createModifiersTable()
    {
        return $this->mockery(ModifiersTable::class);
    }

    /**
     * @param string $parameter
     * @return string
     */
    private function assembleParameterClassName(string $parameter): string
    {
        $basename = implode(array_map(
            function (string $parameterPart) {
                return ucfirst($parameterPart);
            },
            explode('_', $parameter)
        ));

        $namespace = (new \ReflectionClass(Attack::class))->getNamespaceName();

        return $namespace . '\\' . $basename;
    }

    /**
     * @param string $optionalParameter
     * @param string|AbstractTheurgistCode $codeClass
     */
    protected function I_can_get_optional_parameter(string $optionalParameter, string $codeClass)
    {
        $getOptionalParameter = StringTools::assembleGetterForName($optionalParameter);
        $parameterClass = $this->assembleParameterClassName($optionalParameter);
        $sutClass = self::getSutClass();
        $sut = new $sutClass(Tables::getIt(), $this->createModifiersTable());
        $tableArgument = $this->findOutTableArgument($parameterClass);
        foreach ($codeClass::getPossibleValues() as $modifierCode) {
            $expectedParameterValue = $this->getValueFromTable($sut, $modifierCode, $optionalParameter);
            if ($tableArgument) {
                $parameterObject = $sut->$getOptionalParameter(
                    $codeClass::getIt($modifierCode),
                    $tableArgument
                );
                $expectedParameterObject = count($expectedParameterValue) !== 0
                    ? new $parameterClass($expectedParameterValue, $tableArgument)
                    : null;
            } else {
                $parameterObject = $sut->$getOptionalParameter($codeClass::getIt($modifierCode));
                $expectedParameterObject = count($expectedParameterValue) !== 0
                    ? new $parameterClass($expectedParameterValue)
                    : null;
            }
            self::assertEquals($expectedParameterObject, $parameterObject);
        }
    }

    /**
     * @param string $parameterClass
     * @return bool|Table
     */
    private function findOutTableArgument(string $parameterClass)
    {
        $reflectionClass = new \ReflectionClass($parameterClass);
        $constructorReflection = $reflectionClass->getMethod('__construct');
        $parameters = $constructorReflection->getParameters();
        if (count($parameters) === 1) {
            return false;
        }
        $tableParameter = $parameters[1];
        $tableParameterClassReflection = $tableParameter->getClass();
        if (!$tableParameterClassReflection) {
            return null;
        }
        $tableClass = $tableParameter->getClass()->getName();
        self::assertTrue(is_a($tableClass, Table::class, true));

        /** @var Table $table */
        $table = new $tableClass;
        $table->getIndexedValues(); // to load them to provide save result on comparisons of used and unused table

        return $table;
    }
}