<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Deceased;

use Cemetery\Registrar\Domain\Deceased\CauseOfDeath;
use Cemetery\Registrar\Domain\Deceased\DeathCertificateId;
use Cemetery\Registrar\Domain\Deceased\Deceased;
use Cemetery\Registrar\Domain\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Tests\Registrar\Domain\AbstractAggregateRootTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedTest extends AbstractAggregateRootTest
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
        $this->assertInstanceOf(DeceasedId::class, $this->deceased->getId());
        $this->assertSame('D001', (string) $this->deceased->getId());
        $this->assertInstanceOf(NaturalPersonId::class, $this->deceased->getNaturalPersonId());
        $this->assertSame('NP001', (string) $this->deceased->getNaturalPersonId());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->deceased->getDiedAt());
        $this->assertSame('2022-01-10', $this->deceased->getDiedAt()->format('Y-m-d'));
        $this->assertNull($this->deceased->getDeathCertificateId());
        $this->assertNull($this->deceased->getCauseOfDeath());
    }
    
    public function testItSetsDeathCertificateId(): void
    {
        $deathCertificateId = new DeathCertificateId('DC001');
        $this->deceased->setDeathCertificateId($deathCertificateId);
        $this->assertInstanceOf(DeathCertificateId::class, $this->deceased->getDeathCertificateId());
        $this->assertSame('DC001', (string) $this->deceased->getDeathCertificateId());
    }

    public function testItSetsCauseOfDeath(): void
    {
        $causeOfDeath = new CauseOfDeath('Некоторая причина смерти');
        $this->deceased->setCauseOfDeath($causeOfDeath);
        $this->assertInstanceOf(CauseOfDeath::class, $this->deceased->getCauseOfDeath());
        $this->assertSame('Некоторая причина смерти', (string) $this->deceased->getCauseOfDeath());
    }
}
