<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\GeoPosition;

use Cemetery\Registrar\Domain\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\GeoPosition\Error;
use Cemetery\Registrar\Domain\GeoPosition\GeoPosition;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\GeoPosition\GeoPositionType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\JsonTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GeoPositionTypeTest extends JsonTypeTest
{
    protected string $className                                  = GeoPositionType::class;
    protected string $typeName                                   = 'geo_position';
    protected string $phpValueClassName                          = GeoPosition::class;
    protected string $exceptionMessageForDatabaseIncompleteValue = 'Неверный формат геопозиции';

    /**
     * @dataProvider getConversionTests
     */
    public function testItConvertsToDatabaseValue(string $dbValue, GeoPosition $phpValue): void
    {
        $resultingDbValue     = $this->type->convertToDatabaseValue($phpValue, $this->mockPlatform);
        $decodedResultDbValue = \json_decode($resultingDbValue, true);
        $this->assertIsArray($decodedResultDbValue);
        $this->assertArrayHasKey('coordinates', $decodedResultDbValue);
        $this->assertArrayHasKey('error', $decodedResultDbValue);
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
            !\is_null($phpValue->error()) ? (float) $phpValue->error()->value() : null,
            !\is_null($decodedResultDbValue['error']) ? (float) $decodedResultDbValue['error'] : null
        );
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

    protected function getConversionTests(): array
    {
        return [
            // database value,
            // PHP value
            [
                '{"coordinates":{"latitude":54.950357,"longitude":-172.7972252},"error":0.25}',
                new GeoPosition(new Coordinates('54.950357', '-172.7972252'), new Error('0.25'))
            ],
            [
                '{"coordinates":{"latitude":-10.950357,"longitude":72.7972252},"error":null}',
                new GeoPosition(new Coordinates('-10.950357','72.7972252'), null)
            ],
            [
                '{"coordinates":{"latitude":54.950357,"longitude":72.0},"error":1.0}',
                new GeoPosition(new Coordinates('54.950357','72.0'), new Error('1.0'))
            ],
        ];
    }
}
