<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\BurialContainer;

use Cemetery\Registrar\Domain\BurialContainer\Coffin;
use Cemetery\Registrar\Domain\BurialContainer\CoffinShape;
use Cemetery\Registrar\Domain\BurialContainer\CoffinSize;
use Cemetery\Registrar\Domain\BurialContainer\Urn;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\BurialContainer\BurialContainerType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\AbstractTypeTest;
use Doctrine\DBAL\Types\ConversionException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialContainerTypeTest extends AbstractTypeTest
{
    protected string $className = BurialContainerType::class;
    protected string $typeName  = 'burial_container';

    public function testItConvertsToDatabaseValue(): void
    {
        $this->phpValue = new Coffin(new CoffinSize(180), CoffinShape::greekWithHandles(), true);
        $dbValue        = $this->type->convertToDatabaseValue($this->phpValue, $this->mockPlatform);
        $decodedDbValue = \json_decode($dbValue, true);
        $this->assertIsArray($decodedDbValue);
        $this->assertArrayHasKey('type', $decodedDbValue);
        $this->assertArrayHasKey('value', $decodedDbValue);
        $this->assertArrayHasKey('size', $decodedDbValue['value']);
        $this->assertArrayHasKey('shape', $decodedDbValue['value']);
        $this->assertArrayHasKey('isNonStandard', $decodedDbValue['value']);
        $this->assertSame($this->phpValue->className(), $decodedDbValue['type']);
        $this->assertSame($this->phpValue->size()->value(), $decodedDbValue['value']['size']);
        $this->assertSame($this->phpValue->shape()->value(), $decodedDbValue['value']['shape']);
        $this->assertSame($this->phpValue->isNonStandard(), $decodedDbValue['value']['isNonStandard']);

        $this->phpValue = new Urn();
        $dbValue        = $this->type->convertToDatabaseValue($this->phpValue, $this->mockPlatform);
        $decodedDbValue = \json_decode($dbValue, true);
        $this->assertIsArray($decodedDbValue);
        $this->assertArrayHasKey('type', $decodedDbValue);
        $this->assertArrayHasKey('value', $decodedDbValue);
        $this->assertSame($this->phpValue->className(), $decodedDbValue['type']);
        $this->assertNull($decodedDbValue['value']);

        $dbValue = $this->type->convertToDatabaseValue(null, $this->mockPlatform);
        $this->assertNull($dbValue);
    }

    public function testItFailsToConvertPhpValueOfInvalidTypeToDatabaseValue(): void
    {
        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage('Could not convert PHP value \'value of invalid type\' to type burial_container. Expected one of the following types: null, Cemetery\Registrar\Domain\BurialContainer\BurialContainer');
        $this->type->convertToDatabaseValue('value of invalid type', $this->mockPlatform);
    }

    public function testItConvertsToPhpValue(): void
    {
        $this->dbValue  = '{"type":"Coffin","value":{"size":180,"shape":"greek_with_handles","isNonStandard":true}}';
        $decodedDbValue = \json_decode($this->dbValue, true);
        $phpValue       = $this->type->convertToPHPValue($this->dbValue, $this->mockPlatform);
        $this->assertInstanceOf(Coffin::class, $phpValue);
        $this->assertSame($decodedDbValue['value']['size'], $phpValue->size()->value());
        $this->assertSame($decodedDbValue['value']['shape'], $phpValue->shape()->value());
        $this->assertSame($decodedDbValue['value']['isNonStandard'], $phpValue->isNonStandard());

        $this->dbValue = '{"type":"Urn","value":null}';
        $phpValue      = $this->type->convertToPHPValue($this->dbValue, $this->mockPlatform);
        $this->assertInstanceOf(Urn::class, $phpValue);

        $phpValue = $this->type->convertToPHPValue(null, $this->mockPlatform);
        $this->assertNull($phpValue);
    }

    public function testItFailsToConvertDatabaseInvalidJsonValueToPhpValue(): void
    {
        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage('Could not convert database value "value of invalid type" to Doctrine Type burial_container');
        $this->type->convertToPHPValue('value of invalid type', $this->mockPlatform);
    }

    public function testItFailsToConvertDatabaseIncompleteValueToPhpValue(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Неверный формат для контейнера захоронения: "{}".');
        $this->type->convertToPHPValue('{}', $this->mockPlatform);
    }
}
