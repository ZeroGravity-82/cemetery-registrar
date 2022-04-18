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
        $this->assertInstanceOf(DeceasedId::class, $this->deceased->id());
        $this->assertSame('D001', (string) $this->deceased->id());
        $this->assertInstanceOf(NaturalPersonId::class, $this->deceased->naturalPersonId());
        $this->assertSame('NP001', (string) $this->deceased->naturalPersonId());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->deceased->diedAt());
        $this->assertSame('2022-01-10', $this->deceased->diedAt()->format('Y-m-d'));
        $this->assertNull($this->deceased->deathCertificateId());
        $this->assertNull($this->deceased->causeOfDeath());
    }
    
    public function testItSetsDeathCertificateId(): void
    {
        $deathCertificateId = new DeathCertificateId('DC001');
        $this->deceased->setDeathCertificateId($deathCertificateId);
        $this->assertInstanceOf(DeathCertificateId::class, $this->deceased->deathCertificateId());
        $this->assertSame('DC001', (string) $this->deceased->deathCertificateId());
    }

    public function testItSetsCauseOfDeath(): void
    {
        $causeOfDeath = new CauseOfDeath('Некоторая причина смерти');
        $this->deceased->setCauseOfDeath($causeOfDeath);
        $this->assertInstanceOf(CauseOfDeath::class, $this->deceased->causeOfDeath());
        $this->assertSame('Некоторая причина смерти', (string) $this->deceased->causeOfDeath());
    }
}
