<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialContainer;

use Cemetery\Registrar\Domain\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\BurialContainer\Coffin;
use Cemetery\Registrar\Domain\BurialContainer\CoffinShape;
use Cemetery\Registrar\Domain\BurialContainer\CoffinSize;
use Cemetery\Registrar\Domain\BurialContainer\Urn;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialContainer\BurialContainerType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomTypeTest;
use Doctrine\DBAL\Types\ConversionException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialContainerTypeTest extends CustomTypeTest
{
    protected string $className = BurialContainerType::class;
    protected string $typeName  = 'burial_container';

    public function testItConvertsToDatabaseValue(): void
    {
        $this->phpValue = new BurialContainer(new Coffin(new CoffinSize(180), CoffinShape::greekWithHandles(), true));
        $dbValue        = $this->type->convertToDatabaseValue($this->phpValue, $this->mockPlatform);
        $decodedDbValue = \json_decode($dbValue, true);
        $this->assertIsArray($decodedDbValue);
        $this->assertArrayHasKey('class', $decodedDbValue);
        $this->assertArrayHasKey('value', $decodedDbValue);
        $this->assertArrayHasKey('size', $decodedDbValue['value']);
        $this->assertArrayHasKey('shape', $decodedDbValue['value']);
        $this->assertArrayHasKey('isNonStandard', $decodedDbValue['value']);
        $this->assertSame('Coffin', $decodedDbValue['class']);
        $this->assertSame($this->phpValue->container()->size()->value(), $decodedDbValue['value']['size']);
        $this->assertSame($this->phpValue->container()->shape()->value(), $decodedDbValue['value']['shape']);
        $this->assertSame($this->phpValue->container()->isNonStandard(), $decodedDbValue['value']['isNonStandard']);

        $this->phpValue = new BurialContainer(new Urn());
        $dbValue        = $this->type->convertToDatabaseValue($this->phpValue, $this->mockPlatform);
        $decodedDbValue = \json_decode($dbValue, true);
        $this->assertIsArray($decodedDbValue);
        $this->assertArrayHasKey('class', $decodedDbValue);
        $this->assertArrayHasKey('value', $decodedDbValue);
        $this->assertSame('Urn', $decodedDbValue['class']);
        $this->assertNull($decodedDbValue['value']);

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
            BurialContainer::class,
        ));
        $this->type->convertToDatabaseValue($valueOfInvalidType, $this->mockPlatform);
    }

    public function testItConvertsToPhpValue(): void
    {
        $this->dbValue  = '{"class":"Coffin","value":{"size":180,"shape":"GREEK_WITH_HANDLES","isNonStandard":true}}';
        $decodedDbValue = \json_decode($this->dbValue, true);
        $phpValue       = $this->type->convertToPHPValue($this->dbValue, $this->mockPlatform);
        $this->assertInstanceOf(BurialContainer::class, $phpValue);
        $this->assertInstanceOf(Coffin::class, $phpValue->container());
        $this->assertSame($decodedDbValue['value']['size'], $phpValue->container()->size()->value());
        $this->assertSame($decodedDbValue['value']['shape'], $phpValue->container()->shape()->value());
        $this->assertSame($decodedDbValue['value']['isNonStandard'], $phpValue->container()->isNonStandard());

        $this->dbValue = '{"class":"Urn","value":null}';
        $phpValue      = $this->type->convertToPHPValue($this->dbValue, $this->mockPlatform);
        $this->assertInstanceOf(BurialContainer::class, $phpValue);
        $this->assertInstanceOf(Urn::class, $phpValue->container());

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
        $this->expectExceptionMessage('Неверный формат для контейнера захоронения: "{}".');
        $this->type->convertToPHPValue('{}', $this->mockPlatform);
    }
}
