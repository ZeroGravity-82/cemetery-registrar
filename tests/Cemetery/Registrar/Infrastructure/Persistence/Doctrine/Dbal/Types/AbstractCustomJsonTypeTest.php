<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractCustomJsonTypeTest extends AbstractCustomTypeTest
{
    protected string $phpValueClassName;
    protected string $exceptionMessageForDatabaseIncompleteValue;

    abstract protected function getConversionData(): iterable;

    public function testItConvertsToDatabaseNullValue(): void
    {
        $resultingDbValue = $this->type->convertToDatabaseValue(null, $this->mockPlatform);
        $this->assertNull($resultingDbValue);
    }

    public function testItFailsToConvertPhpValueOfInvalidTypeToDatabaseValue(): void
    {
        $valueOfInvalidType = new \stdClass();
        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage(\sprintf(
            'Could not convert PHP value of type %s to type %s. Expected one of the following types: null, %s',
            \get_class($valueOfInvalidType),
            $this->typeName,
            $this->phpValueClassName,
        ));
        $this->type->convertToDatabaseValue($valueOfInvalidType, $this->mockPlatform);
    }

    public function testItConvertsToPhpNullValue(): void
    {
        $resultingPhpValue = $this->type->convertToPHPValue(null, $this->mockPlatform);
        $this->assertNull($resultingPhpValue);
    }

    public function testItFailsToConvertDatabaseInvalidJsonValueToPhpValue(): void
    {
        $valueOfInvalidType = 'value of invalid type';
        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage(\sprintf(
            'Could not convert database value "%s" to Doctrine Type %s',
            $valueOfInvalidType,
            $this->typeName,
        ));
        $this->type->convertToPHPValue($valueOfInvalidType, $this->mockPlatform);
    }

    public function testItFailsToConvertDatabaseIncompleteValueToPhpValue(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('%s: "{}".', $this->exceptionMessageForDatabaseIncompleteValue));
        $this->type->convertToPHPValue('{}', $this->mockPlatform);
    }
}
