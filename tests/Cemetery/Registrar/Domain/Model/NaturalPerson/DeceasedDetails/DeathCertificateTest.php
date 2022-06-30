<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\NaturalPerson\DeceasedDetails;

use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\DeathCertificate;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeathCertificateTest extends TestCase
{
    private string             $seriesA;
    private string             $seriesB;
    private string             $numberA;
    private string             $numberB;
    private \DateTimeImmutable $issuedAtA;
    private \DateTimeImmutable $issuedAtB;

    public function setUp(): void
    {
        $this->seriesA   = 'V-МЮ';
        $this->seriesB   = 'I-BC';
        $this->numberA   = '532515';
        $this->numberB   = '785066';
        $this->issuedAtA = new \DateTimeImmutable('2002-10-28');
        $this->issuedAtB = new \DateTimeImmutable('2011-03-23');
    }

    public function testItSuccessfullyCreated(): void
    {
        $deathCertificate = new DeathCertificate(
            $this->seriesA,
            $this->numberA,
            $this->issuedAtA,
        );
        $this->assertSame($this->seriesA, $deathCertificate->series());
        $this->assertSame($this->numberA, $deathCertificate->number());
        $this->assertSame($this->issuedAtA->format('Y-m-d'), $deathCertificate->issuedAt()->format('Y-m-d'));
    }

    public function testItFailsWithEmptySeriesValue(): void
    {
        $this->expectExceptionForEmptyValue('Серия свидетельства о смерти');
        new DeathCertificate(
            '',
            $this->numberA,
            $this->issuedAtA,
        );
    }

    public function testItFailsWithSeriesValueOfSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue('Серия свидетельства о смерти');
        new DeathCertificate(
            '   ',
            $this->numberA,
            $this->issuedAtA,
        );
    }

    public function testItFailsWithEmptyNumberValue(): void
    {
        $this->expectExceptionForEmptyValue('Номер свидетельства о смерти');
        new DeathCertificate(
            $this->seriesA,
            '',
            $this->issuedAtA,
        );
    }

    public function testItFailsWithNumberValueOfSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue('Номер свидетельства о смерти');
        new DeathCertificate(
            $this->seriesA,
            '   ',
            $this->issuedAtA,
        );
    }

    public function testItFailsWithFutureIssuedAtValue(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Дата выдачи свидетельства о смерти не может иметь значение из будущего.');
        new DeathCertificate(
            $this->seriesA,
            $this->numberA,
            (new \DateTimeImmutable())->modify('+1 day'),
        );
    }

    public function testItComparable(): void
    {
        $deathCertificateA = new DeathCertificate(
            $this->seriesA,
            $this->numberA,
            $this->issuedAtA,
        );
        $deathCertificateB = new DeathCertificate(
            $this->seriesB,
            $this->numberA,
            $this->issuedAtA,
        );
        $deathCertificateC = new DeathCertificate(
            $this->seriesA,
            $this->numberB,
            $this->issuedAtA,
        );
        $deathCertificateD = new DeathCertificate(
            $this->seriesA,
            $this->numberA,
            $this->issuedAtB,
        );
        $deathCertificateE = new DeathCertificate(
            $this->seriesA,
            $this->numberA,
            $this->issuedAtA,
        );

        $this->assertFalse($deathCertificateA->isEqual($deathCertificateB));
        $this->assertFalse($deathCertificateA->isEqual($deathCertificateC));
        $this->assertFalse($deathCertificateA->isEqual($deathCertificateD));
        $this->assertTrue($deathCertificateA->isEqual($deathCertificateE));
        $this->assertFalse($deathCertificateB->isEqual($deathCertificateC));
        $this->assertFalse($deathCertificateB->isEqual($deathCertificateD));
        $this->assertFalse($deathCertificateB->isEqual($deathCertificateE));
        $this->assertFalse($deathCertificateC->isEqual($deathCertificateD));
        $this->assertFalse($deathCertificateC->isEqual($deathCertificateE));
        $this->assertFalse($deathCertificateD->isEqual($deathCertificateE));
    }

    private function expectExceptionForEmptyValue(string $name): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            \sprintf('%s не может иметь пустое значение.', $name)
        );
    }
}
