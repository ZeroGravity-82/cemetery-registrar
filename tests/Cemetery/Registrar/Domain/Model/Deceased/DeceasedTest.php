<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Deceased;

use Cemetery\Registrar\Domain\Model\Deceased\Age;
use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath;
use Cemetery\Registrar\Domain\Model\Deceased\DeathCertificateId;
use Cemetery\Registrar\Domain\Model\Deceased\Deceased;
use Cemetery\Registrar\Domain\Model\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Tests\Registrar\Domain\Model\AggregateRootTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedTest extends AggregateRootTest
{
    private Deceased $deceased;
    
    public function setUp(): void
    {
        $id              = new DeceasedId('D001');
        $naturalPersonId = new NaturalPersonId('NP001');
        $diedAt          = new \DateTimeImmutable('2022-01-10');
        $this->deceased  = new Deceased($id, $naturalPersonId, $diedAt);
        $this->entity    = $this->deceased;
    }
    
    public function testItSuccessfullyCreated(): void
    {
        $this->assertInstanceOf(DeceasedId::class, $this->deceased->id());
        $this->assertSame('D001', (string) $this->deceased->id());
        $this->assertInstanceOf(NaturalPersonId::class, $this->deceased->naturalPersonId());
        $this->assertSame('NP001', (string) $this->deceased->naturalPersonId());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->deceased->diedAt());
        $this->assertSame('2022-01-10', $this->deceased->diedAt()->format('Y-m-d'));
        $this->assertNull($this->deceased->age());
        $this->assertNull($this->deceased->deathCertificateId());
        $this->assertNull($this->deceased->causeOfDeath());
    }

    public function testItSetsNaturalPersonId(): void
    {
        $naturalPersonId = new NaturalPersonId('NP002');
        $this->deceased->setNaturalPersonId($naturalPersonId);
        $this->assertInstanceOf(NaturalPersonId::class, $this->deceased->naturalPersonId());
        $this->assertTrue($this->deceased->naturalPersonId()->isEqual($naturalPersonId));
    }

    public function testItSetsDiedAt(): void
    {
        $diedAt = new \DateTimeImmutable('2022-01-11');
        $this->deceased->setDiedAt($diedAt);
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->deceased->diedAt());
        $this->assertSame('2022-01-11', $this->deceased->diedAt()->format('Y-m-d'));
    }

    public function testItSetsAge(): void
    {
        $age = new Age(82);
        $this->deceased->setAge($age);
        $this->assertInstanceOf(Age::class, $this->deceased->age());
        $this->assertTrue($this->deceased->age()->isEqual($age));

        $this->deceased->setAge(null);
        $this->assertNull($this->deceased->age());
    }

    public function testItSetsDeathCertificateId(): void
    {
        $deathCertificateId = new DeathCertificateId('DC001');
        $this->deceased->setDeathCertificateId($deathCertificateId);
        $this->assertInstanceOf(DeathCertificateId::class, $this->deceased->deathCertificateId());
        $this->assertTrue($this->deceased->deathCertificateId()->isEqual($deathCertificateId));

        $this->deceased->setDeathCertificateId(null);
        $this->assertNull($this->deceased->deathCertificateId());
    }

    public function testItSetsCauseOfDeath(): void
    {
        $causeOfDeath = new CauseOfDeath('Некоторая причина смерти');
        $this->deceased->setCauseOfDeath($causeOfDeath);
        $this->assertInstanceOf(CauseOfDeath::class, $this->deceased->causeOfDeath());
        $this->assertTrue($this->deceased->causeOfDeath()->isEqual($causeOfDeath));

        $this->deceased->setCauseOfDeath(null);
        $this->assertNull($this->deceased->causeOfDeath());
    }
}
