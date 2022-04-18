<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractPolymorphicIdTypeTest extends AbstractTypeTest
{
    public function testItConvertsToDatabaseValue(): void
    {
        $dbValue        = $this->type->convertToDatabaseValue($this->phpValue, $this->mockPlatform);
        $decodedDbValue = \json_decode($dbValue, true);
        $this->assertIsArray($decodedDbValue);
        $this->assertArrayHasKey('type', $decodedDbValue);
        $this->assertArrayHasKey('value', $decodedDbValue);
        $this->assertSame($this->phpValue->idType(), $decodedDbValue['type']);
        $this->assertSame($this->phpValue->id()->value(), $decodedDbValue['value']);

        $dbValue = $this->type->convertToDatabaseValue(null, $this->mockPlatform);
        $this->assertNull($dbValue);
    }

    public function testItFailsToConvertPhpValueOfInvalidTypeToDatabaseValue(): void
    {
        $valueOfInvalidType = 'value of invalid type';
        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage(\sprintf(
            'Could not convert PHP value \'%s\' to type %s. Expected one of the following types: null, %s',
            $valueOfInvalidType,
            $this->typeName,
            $this->phpValue::class,
        ));
        $this->type->convertToDatabaseValue($valueOfInvalidType, $this->mockPlatform);
    }

    public function testItConvertsToPhpValue(): void
    {
        $phpValue       = $this->type->convertToPHPValue($this->dbValue, $this->mockPlatform);
        $decodedDbValue = \json_decode($this->dbValue, true);
        $this->assertArrayHasKey('type', $decodedDbValue);
        $this->assertArrayHasKey('value', $decodedDbValue);
        $this->assertInstanceOf(\get_class($this->phpValue), $phpValue);
        $this->assertSame($decodedDbValue['type'], $phpValue->idType());
        $this->assertSame($decodedDbValue['value'], $phpValue->id()->value());

        $phpValue = $this->type->convertToPHPValue(null, $this->mockPlatform);
        $this->assertNull($phpValue);
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
        $this->expectExceptionMessage('Неверный формат для полиморфного идентификатора: "{}".');
        $this->type->convertToPHPValue('{}', $this->mockPlatform);
    }
}
