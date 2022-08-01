<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\NaturalPerson\DeceasedDetails;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\Age;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\CremationCertificate;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\DeathCertificate;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\DeceasedDetails;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedDetailsTest extends TestCase
{
    private \DateTimeImmutable   $diedAtA;
    private \DateTimeImmutable   $diedAtB;
    private Age                  $ageA;
    private Age                  $ageB;
    private CauseOfDeathId       $causeOfDeathIdA;
    private CauseOfDeathId       $causeOfDeathIdB;
    private DeathCertificate     $deathCertificateA;
    private DeathCertificate     $deathCertificateB;
    private CremationCertificate $cremationCertificateA;
    private CremationCertificate $cremationCertificateB;

    public function setUp(): void
    {
        $this->diedAtA               = new \DateTimeImmutable('2011-04-30');
        $this->diedAtB               = new \DateTimeImmutable('2021-12-15');
        $this->ageA                  = new Age(82);
        $this->ageB                  = new Age(26);
        $this->causeOfDeathIdA       = new CauseOfDeathId('CD001');
        $this->causeOfDeathIdB       = new CauseOfDeathId('CD002');
        $this->deathCertificateA     = new DeathCertificate('V-МЮ', '532515', new \DateTimeImmutable('2002-10-28'));
        $this->deathCertificateB     = new DeathCertificate('I-BC', '785066', new \DateTimeImmutable('2011-03-24'));
        $this->cremationCertificateA = new CremationCertificate('12964', new \DateTimeImmutable('2002-10-29'));
        $this->cremationCertificateB = new CremationCertificate('811/19', new \DateTimeImmutable('2011-03-23'));
    }

    public function testItSuccessfullyCreated(): void
    {
        $deceasedDetails = new DeceasedDetails(
            $this->diedAtA,
            $this->ageA,
            $this->causeOfDeathIdA,
            $this->deathCertificateA,
            $this->cremationCertificateA,
        );
        $this->assertInstanceOf(\DateTimeImmutable::class, $deceasedDetails->diedAt());
        $this->assertSame($this->diedAtA->format('Y-m-d'), $deceasedDetails->diedAt()->format('Y-m-d'));
        $this->assertInstanceOf(Age::class, $deceasedDetails->age());
        $this->assertTrue($this->ageA->isEqual($deceasedDetails->age()));
        $this->assertInstanceOf(CauseOfDeathId::class, $deceasedDetails->causeOfDeathId());
        $this->assertTrue($this->causeOfDeathIdA->isEqual($deceasedDetails->causeOfDeathId()));
        $this->assertInstanceOf(DeathCertificate::class, $deceasedDetails->deathCertificate());
        $this->assertTrue($this->deathCertificateA->isEqual($deceasedDetails->deathCertificate()));
        $this->assertInstanceOf(CremationCertificate::class, $deceasedDetails->cremationCertificate());
        $this->assertTrue($this->cremationCertificateA->isEqual($deceasedDetails->cremationCertificate()));
    }

    public function testItSuccessfullyCreatedWithoutOptionalFields(): void
    {
        $deceasedDetails = new DeceasedDetails(
            $this->diedAtB,
            null,
            null,
            null,
            null,
        );
        $this->assertInstanceOf(\DateTimeImmutable::class, $deceasedDetails->diedAt());
        $this->assertSame($this->diedAtB->format('Y-m-d'), $deceasedDetails->diedAt()->format('Y-m-d'));
        $this->assertNull($deceasedDetails->age());
        $this->assertNull($deceasedDetails->causeOfDeathId());
        $this->assertNull($deceasedDetails->deathCertificate());
        $this->assertNull($deceasedDetails->cremationCertificate());
    }

    public function testItFailsWithInvalidDiedAtFormat(): void
    {
        $this->markTestIncomplete();
    }

    public function testItFailsWithInvalidDeathCertificateIssuedAtFormat(): void
    {
        $this->markTestIncomplete();
    }

    public function testItFailsWithInvalidCremationCertificateIssuedAtFormat(): void
    {
        $this->markTestIncomplete();
    }

    public function testItComparable(): void
    {
        $deceasedDetailsA = new DeceasedDetails(
            $this->diedAtA,
            $this->ageA,
            $this->causeOfDeathIdA,
            $this->deathCertificateA,
            $this->cremationCertificateA,
        );
        $deceasedDetailsB = new DeceasedDetails(
            $this->diedAtA,
            null,
            null,
            null,
            null,
        );
        $deceasedDetailsC = new DeceasedDetails(
            $this->diedAtB,
            $this->ageA,
            $this->causeOfDeathIdA,
            $this->deathCertificateA,
            $this->cremationCertificateA,
        );
        $deceasedDetailsD = new DeceasedDetails(
            $this->diedAtA,
            $this->ageB,
            $this->causeOfDeathIdA,
            $this->deathCertificateA,
            $this->cremationCertificateA,
        );
        $deceasedDetailsE = new DeceasedDetails(
            $this->diedAtA,
            $this->ageA,
            $this->causeOfDeathIdB,
            $this->deathCertificateA,
            $this->cremationCertificateA,
        );
        $deceasedDetailsF = new DeceasedDetails(
            $this->diedAtA,
            $this->ageA,
            $this->causeOfDeathIdA,
            $this->deathCertificateB,
            $this->cremationCertificateA,
        );
        $deceasedDetailsG = new DeceasedDetails(
            $this->diedAtA,
            $this->ageA,
            $this->causeOfDeathIdA,
            $this->deathCertificateA,
            $this->cremationCertificateB,
        );
        $deceasedDetailsH = new DeceasedDetails(
            $this->diedAtA,
            $this->ageA,
            $this->causeOfDeathIdA,
            $this->deathCertificateA,
            $this->cremationCertificateA,
        );

        $this->assertFalse($deceasedDetailsA->isEqual($deceasedDetailsB));
        $this->assertFalse($deceasedDetailsA->isEqual($deceasedDetailsC));
        $this->assertFalse($deceasedDetailsA->isEqual($deceasedDetailsD));
        $this->assertFalse($deceasedDetailsA->isEqual($deceasedDetailsE));
        $this->assertFalse($deceasedDetailsA->isEqual($deceasedDetailsF));
        $this->assertFalse($deceasedDetailsA->isEqual($deceasedDetailsG));
        $this->assertTrue($deceasedDetailsA->isEqual($deceasedDetailsH));
        $this->assertFalse($deceasedDetailsB->isEqual($deceasedDetailsC));
        $this->assertFalse($deceasedDetailsB->isEqual($deceasedDetailsD));
        $this->assertFalse($deceasedDetailsB->isEqual($deceasedDetailsE));
        $this->assertFalse($deceasedDetailsB->isEqual($deceasedDetailsF));
        $this->assertFalse($deceasedDetailsB->isEqual($deceasedDetailsG));
        $this->assertFalse($deceasedDetailsB->isEqual($deceasedDetailsH));
        $this->assertFalse($deceasedDetailsC->isEqual($deceasedDetailsD));
        $this->assertFalse($deceasedDetailsC->isEqual($deceasedDetailsE));
        $this->assertFalse($deceasedDetailsC->isEqual($deceasedDetailsF));
        $this->assertFalse($deceasedDetailsC->isEqual($deceasedDetailsG));
        $this->assertFalse($deceasedDetailsC->isEqual($deceasedDetailsH));
        $this->assertFalse($deceasedDetailsD->isEqual($deceasedDetailsE));
        $this->assertFalse($deceasedDetailsD->isEqual($deceasedDetailsF));
        $this->assertFalse($deceasedDetailsD->isEqual($deceasedDetailsG));
        $this->assertFalse($deceasedDetailsD->isEqual($deceasedDetailsH));
        $this->assertFalse($deceasedDetailsE->isEqual($deceasedDetailsF));
        $this->assertFalse($deceasedDetailsE->isEqual($deceasedDetailsG));
        $this->assertFalse($deceasedDetailsE->isEqual($deceasedDetailsH));
        $this->assertFalse($deceasedDetailsF->isEqual($deceasedDetailsG));
        $this->assertFalse($deceasedDetailsF->isEqual($deceasedDetailsH));
        $this->assertFalse($deceasedDetailsG->isEqual($deceasedDetailsH));
    }
}
