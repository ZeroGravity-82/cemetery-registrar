<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Cemetery\Registrar\Domain\EntityId;
use Cemetery\Registrar\Domain\EntityMaskingId;
use Doctrine\DBAL\Types\ConversionException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class MaskingIdTypeTest extends TypeTest
{
    protected string $phpValueClassName;

    abstract protected function getConversionTests(): array;

    /**
     * @dataProvider getConversionTests
     */
    public function testItConvertsToDatabaseValue(string $dbValue, EntityMaskingId $phpValue): void
    {
        $resultingDbValue     = $this->type->convertToDatabaseValue($phpValue, $this->mockPlatform);
        $decodedResultDbValue = \json_decode($resultingDbValue, true);
        $this->assertIsArray($decodedResultDbValue);
        $this->assertArrayHasKey('class', $decodedResultDbValue);
        $this->assertArrayHasKey('value', $decodedResultDbValue);
        $this->assertSame($this->getClass($phpValue->id()), $decodedResultDbValue['class']);
        $this->assertSame($phpValue->id()->value(), $decodedResultDbValue['value']);
    }

    public function testItConvertsToDatabaseNullValue(): void
    {
        $resultingDbValue = $this->type->convertToDatabaseValue(null, $this->mockPlatform);
        $this->assertNull($resultingDbValue);
    }

    public function testItFailsToConvertPhpValueOfInvalidTypeToDatabaseValue(): void
    {
        $valueOfInvalidType = 'value of invalid type';
        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage(\sprintf(
            'Could not convert PHP value \'%s\' to type %s. Expected one of the following types: null, %s',
            $valueOfInvalidType,
            $this->typeName,
            $this->phpValueClassName,
        ));
        $this->type->convertToDatabaseValue($valueOfInvalidType, $this->mockPlatform);
    }

    /**
     * @dataProvider getConversionTests
     */
    public function testItConvertsToPhpValue(string $dbValue, EntityMaskingId $phpValue): void
    {
        $resultingPhpValue = $this->type->convertToPHPValue($dbValue, $this->mockPlatform);
        $this->assertInstanceOf(\get_class($phpValue), $resultingPhpValue);
        $this->assertSame($this->getClass($phpValue->id()), $this->getClass($resultingPhpValue->id()));
        $this->assertSame($phpValue->id()->value(), $resultingPhpValue->id()->value());
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
        $this->expectExceptionMessage('Неверный формат идентификатора: "{}".');
        $this->type->convertToPHPValue('{}', $this->mockPlatform);
    }

    private function getClass(EntityId $id): string
    {
        $parts = \explode('\\', \get_class($id));

        return \end($parts);
    }
}
