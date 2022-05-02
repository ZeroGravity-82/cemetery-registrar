<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\GeoPosition;

use Cemetery\Registrar\Domain\GeoPosition\Accuracy;
use Cemetery\Registrar\Domain\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\GeoPosition\GeoPosition;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\GeoPosition\GeoPositionType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\TypeTest;
use Doctrine\DBAL\Types\ConversionException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GeoPositionTypeTest extends TypeTest
{
    protected string $className         = GeoPositionType::class;
    protected string $typeName          = 'geo_position';
    protected string $phpValueClassName = GeoPosition::class;

    /**
     * @dataProvider getConversionTests
     */
    public function testItConvertsToDatabaseValue(string $dbValue, GeoPosition $phpValue): void
    {
        $resultingDbValue     = $this->type->convertToDatabaseValue($phpValue, $this->mockPlatform);
        $decodedResultDbValue = \json_decode($resultingDbValue, true);
        $this->assertIsArray($decodedResultDbValue);
        $this->assertArrayHasKey('coordinates', $decodedResultDbValue);
        $this->assertArrayHasKey('accuracy', $decodedResultDbValue);
        $this->assertIsArray($decodedResultDbValue['coordinates']);
        $this->assertArrayHasKey('latitude', $decodedResultDbValue['coordinates']);
        $this->assertArrayHasKey('longitude', $decodedResultDbValue['coordinates']);
        $this->assertSame(
            (float) $phpValue->coordinates()->latitude(),
            (float) $decodedResultDbValue['coordinates']['latitude']
        );
        $this->assertSame(
            (float) $phpValue->coordinates()->longitude(),
            (float) $decodedResultDbValue['coordinates']['longitude']
        );
        $this->assertSame(
            !\is_null($phpValue->accuracy()) ? (float) $phpValue->accuracy()->value() : null,
            !\is_null($decodedResultDbValue['accuracy']) ? (float) $decodedResultDbValue['accuracy'] : null
        );
    }

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

    /**
     * @dataProvider getConversionTests
     */
    public function testItConvertsToPhpValue(string $dbValue, GeoPosition $phpValue): void
    {
        $resultingPhpValue = $this->type->convertToPHPValue($dbValue, $this->mockPlatform);
        $this->assertInstanceOf(GeoPosition::class, $resultingPhpValue);
        $this->assertTrue($resultingPhpValue->isEqual($phpValue));
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
        $this->expectExceptionMessage('Неверный формат геопозиции: "{}".');
        $this->type->convertToPHPValue('{}', $this->mockPlatform);
    }

    private function getConversionTests(): array
    {
        return [
            // database value,
            // PHP value
            [
                '{"coordinates":{"latitude":54.950357,"longitude":-172.7972252},"accuracy":0.25}',
                new GeoPosition(new Coordinates('54.950357', '-172.7972252'), new Accuracy('0.25'))
            ],
            [
                '{"coordinates":{"latitude":-10.950357,"longitude":72.7972252},"accuracy":null}',
                new GeoPosition(new Coordinates('-10.950357','72.7972252'), null)
            ],
            [
                '{"coordinates":{"latitude":54.950357,"longitude":72.0},"accuracy":1.0}',
                new GeoPosition(new Coordinates('54.950357','72.0'), new Accuracy('1.0'))
            ],
        ];
    }
}
