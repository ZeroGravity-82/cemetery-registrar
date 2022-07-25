<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\NaturalPerson\DeceasedDetails;

use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\DeceasedDetails;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\DeceasedDetailsFactory;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedDetailsFactoryTest extends TestCase
{
    private string                 $diedAt                       = '2011-04-30';
    private int                    $age                          = 82;
    private string                 $causeOfDeathId               = 'CD001';
    private string                 $deathCertificateSeries       = 'V-МЮ';
    private string                 $deathCertificateNumber       = '532515';
    private string                 $deathCertificateIssuedAt     = '2002-10-28';
    private string                 $cremationCertificateNumber   = '12964';
    private string                 $cremationCertificateIssuedAt = '2002-10-29';
    private DeceasedDetailsFactory $deceasedDetailsFactory;

    public function setUp(): void
    {
        $this->deceasedDetailsFactory = new DeceasedDetailsFactory();
    }

    public function testItCreatesDeceasedDetails(): void
    {
        $deceasedDetails = $this->deceasedDetailsFactory->create(
            $this->diedAt,
            $this->age,
            $this->causeOfDeathId,
            $this->deathCertificateSeries,
            $this->deathCertificateNumber,
            $this->deathCertificateIssuedAt,
            $this->cremationCertificateNumber,
            $this->cremationCertificateIssuedAt,
        );
        $this->assertInstanceOf(DeceasedDetails::class, $deceasedDetails);
        $this->assertSame($this->diedAt, $deceasedDetails->diedAt()->format('Y-m-d'));
        $this->assertSame($this->age, $deceasedDetails->age()->value());
        $this->assertSame($this->causeOfDeathId, $deceasedDetails->causeOfDeathId()->value());
        $this->assertSame($this->deathCertificateSeries, $deceasedDetails->deathCertificate()->series());
        $this->assertSame($this->deathCertificateNumber, $deceasedDetails->deathCertificate()->number());
        $this->assertSame($this->deathCertificateIssuedAt, $deceasedDetails->deathCertificate()->issuedAt()->format('Y-m-d'));
        $this->assertSame($this->cremationCertificateNumber, $deceasedDetails->cremationCertificate()->number());
        $this->assertSame($this->cremationCertificateIssuedAt, $deceasedDetails->cremationCertificate()->issuedAt()->format('Y-m-d'));
    }

    public function testItCreatesDeceasedDetailsWithoutOptionalFields(): void
    {
        $deceasedDetails = $this->deceasedDetailsFactory->create(
            $this->diedAt,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
        );
        $this->assertInstanceOf(DeceasedDetails::class, $deceasedDetails);
        $this->assertSame($this->diedAt, $deceasedDetails->diedAt()->format('Y-m-d'));
        $this->assertNull($deceasedDetails->age());
        $this->assertNull($deceasedDetails->causeOfDeathId());
        $this->assertNull($deceasedDetails->deathCertificate());
        $this->assertNull($deceasedDetails->cremationCertificate());
    }

    public function testItFailsWithIncompleteDeathCertificateDataA(): void
    {
        $this->expectExceptionForIncompleteData('свидетельства о смерти', 'серии');
        $this->deceasedDetailsFactory->create(
            $this->diedAt,
            $this->age,
            $this->causeOfDeathId,
            null,
            $this->deathCertificateNumber,
            $this->deathCertificateIssuedAt,
            $this->cremationCertificateNumber,
            $this->cremationCertificateIssuedAt,
        );
    }

    public function testItFailsWithIncompleteDeathCertificateDataB(): void
    {
        $this->expectExceptionForIncompleteData('свидетельства о смерти', 'номера');
        $this->deceasedDetailsFactory->create(
            $this->diedAt,
            $this->age,
            $this->causeOfDeathId,
            $this->deathCertificateSeries,
            null,
            $this->deathCertificateIssuedAt,
            $this->cremationCertificateNumber,
            $this->cremationCertificateIssuedAt,
        );
    }

    public function testItFailsWithIncompleteDeathCertificateDataC(): void
    {
        $this->expectExceptionForIncompleteData('свидетельства о смерти', 'даты выдачи');
        $this->deceasedDetailsFactory->create(
            $this->diedAt,
            $this->age,
            $this->causeOfDeathId,
            $this->deathCertificateSeries,
            $this->deathCertificateNumber,
            null,
            $this->cremationCertificateNumber,
            $this->cremationCertificateIssuedAt,
        );
    }

    public function testItFailsWithIncompleteCremationCertificateDataA(): void
    {
        $this->expectExceptionForIncompleteData('справки о смерти', 'номера');
        $this->deceasedDetailsFactory->create(
            $this->diedAt,
            $this->age,
            $this->causeOfDeathId,
            $this->deathCertificateSeries,
            $this->deathCertificateNumber,
            $this->deathCertificateIssuedAt,
            null,
            $this->cremationCertificateIssuedAt,
        );
    }

    public function testItFailsWithIncompleteCremationCertificateDataB(): void
    {
        $this->expectExceptionForIncompleteData('справки о смерти', 'даты выдачи');
        $this->deceasedDetailsFactory->create(
            $this->diedAt,
            $this->age,
            $this->causeOfDeathId,
            $this->deathCertificateSeries,
            $this->deathCertificateNumber,
            $this->deathCertificateIssuedAt,
            $this->cremationCertificateNumber,
            null,
        );
    }

    private function expectExceptionForIncompleteData(string $documentName, string $fieldName): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf(
            'Неполные данные %s: не указано значение для %s.',
            $documentName,
            $fieldName,
        ));
    }
}
