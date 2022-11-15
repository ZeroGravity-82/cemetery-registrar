<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\NaturalPerson\DeceasedDetails;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\Age;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\CremationCertificate;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\DeathCertificate;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\DeceasedDetails;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\NaturalPerson\DeceasedDetails\DeceasedDetailsType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomJsonTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedDetailsTypeTest extends AbstractCustomJsonTypeTest
{
    protected string $className                                  = DeceasedDetailsType::class;
    protected string $typeName                                   = 'deceased_details';
    protected string $phpValueClassName                          = DeceasedDetails::class;
    protected string $exceptionMessageForDatabaseIncompleteValue = 'Неверный формат декодированного значения для данных умершего';

    /**
     * @dataProvider getConversionData
     */
    public function testItConvertsToDatabaseValue(string $dbValue, DeceasedDetails $phpValue): void
    {
        $resultingDbValue = $this->type->convertToDatabaseValue($phpValue, $this->mockPlatform);
        $this->assertJson($resultingDbValue);
        $decodedResultDbValue = \json_decode($resultingDbValue, true);
        $this->assertIsArray($decodedResultDbValue);
        $this->assertArrayHasKey('diedAt', $decodedResultDbValue);
        $this->assertArrayHasKey('age', $decodedResultDbValue);
        $this->assertArrayHasKey('causeOfDeathId', $decodedResultDbValue);
        $this->assertArrayHasKey('deathCertificate', $decodedResultDbValue);
        $this->assertArrayHasKey('cremationCertificate', $decodedResultDbValue);
        $this->assertSame($phpValue->diedAt()->format('Y-m-d'), $decodedResultDbValue['diedAt']);
        $this->assertSame($phpValue->age()?->value(), $decodedResultDbValue['age']);
        $this->assertSame($phpValue->causeOfDeathId()?->value(), $decodedResultDbValue['causeOfDeathId']);
        $this->assertSame(
            $phpValue->deathCertificate()?->series(),
            $decodedResultDbValue['deathCertificate']['series'] ?? null
        );
        $this->assertSame(
            $phpValue->deathCertificate()?->number(),
            $decodedResultDbValue['deathCertificate']['number'] ?? null
        );
        $this->assertSame(
            $phpValue->deathCertificate()?->issuedAt()->format('Y-m-d'),
            $decodedResultDbValue['deathCertificate']['issuedAt'] ?? null
        );
        $this->assertSame(
            $phpValue->cremationCertificate()?->number(),
            $decodedResultDbValue['cremationCertificate']['number'] ?? null
        );
        $this->assertSame(
            $phpValue->cremationCertificate()?->issuedAt()->format('Y-m-d'),
            $decodedResultDbValue['cremationCertificate']['issuedAt'] ?? null
        );
    }

    /**
     * @dataProvider getConversionData
     */
    public function testItConvertsToPhpValue(string $dbValue, DeceasedDetails $phpValue): void
    {
        $resultingPhpValue = $this->type->convertToPHPValue($dbValue, $this->mockPlatform);
        $this->assertInstanceOf(DeceasedDetails::class, $resultingPhpValue);
        $this->assertTrue($resultingPhpValue->isEqual($phpValue));
    }

    protected function getConversionData(): iterable
    {
        // database value,
        // PHP value
        yield [
            <<<JSON_A
{
  "diedAt": "2011-04-30",
  "age": 82,
  "causeOfDeathId": "CD001",
  "deathCertificate": {
    "series": "V-МЮ",
    "number": "532515",
    "issuedAt": "2002-10-28"
  },
  "cremationCertificate": {
    "number": "12964",
    "issuedAt": "2002-10-29"
  }
}
JSON_A
            ,
            new DeceasedDetails(
                new \DateTimeImmutable('2011-04-30'),
                new Age(82),
                new CauseOfDeathId('CD001'),
                new DeathCertificate('V-МЮ', '532515', new \DateTimeImmutable('2002-10-28')),
                new CremationCertificate('12964', new \DateTimeImmutable('2002-10-29')),
            ),
        ];
        yield [
            <<<JSON_B
{
  "naturalPersonId": "NP002",
  "diedAt": "2021-12-15",
  "age": null,
  "causeOfDeathId": null,
  "deathCertificate": null,
  "cremationCertificate": null
}
JSON_B
            ,
            new DeceasedDetails(
                new \DateTimeImmutable('2021-12-15'),
                null,
                null,
                null,
                null,
            )
        ];
    }

    public function testItFailsWithInvalidDiedAtDateFormat(): void
    {
        $this->markTestIncomplete();
    }

    public function testItFailsWithInvalidDeathCertificateIssuedAtDateFormat(): void
    {
        $this->markTestIncomplete();
    }

    public function testItFailsWithInvalidCremationCertificateIssuedAtDateFormat(): void
    {
        $this->markTestIncomplete();
    }
}
