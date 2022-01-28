<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Deceased;

use Cemetery\Registrar\Domain\Deceased\CauseOfDeath;
use Cemetery\Registrar\Domain\Deceased\DeathCertificateId;
use Cemetery\Registrar\Domain\Deceased\Deceased;
use Cemetery\Registrar\Domain\Deceased\DeceasedCollection;
use Cemetery\Registrar\Domain\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Tests\Registrar\Domain\AbstractEntityCollectionTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedCollectionTest extends AbstractEntityCollectionTest
{
    public function setUp(): void
    {
        $this->idA           = new DeceasedId('D001');
        $this->idB           = new DeceasedId('D002');
        $this->idC           = new DeceasedId('D003');
        $this->idD           = new DeceasedId('D004');
        $naturalPersonIdA    = new NaturalPersonId('NP001');
        $naturalPersonIdB    = new NaturalPersonId('NP002');
        $naturalPersonIdC    = new NaturalPersonId('NP003');
        $naturalPersonIdD    = new NaturalPersonId('NP004');
        $diedAtA             = new \DateTimeImmutable('2021-12-01');
        $diedAtB             = new \DateTimeImmutable('2001-02-11');
        $diedAtC             = new \DateTimeImmutable('2011-05-13');
        $diedAtD             = new \DateTimeImmutable('2015-03-10');
        $deathCertificateIdB = new DeathCertificateId('DC001');
        $deathCertificateIdC = new DeathCertificateId('DC002');
        $causeOfDeathB       = new CauseOfDeath('Some cause 1');
        $this->entityA       = new Deceased($this->idA, $naturalPersonIdA, $diedAtA, null, null);
        $this->entityB       = new Deceased($this->idB, $naturalPersonIdB, $diedAtB, $deathCertificateIdB, $causeOfDeathB);
        $this->entityC       = new Deceased($this->idC, $naturalPersonIdC, $diedAtC, $deathCertificateIdC, null);
        $this->entityD       = new Deceased($this->idD, $naturalPersonIdD, $diedAtD, null, null);
        $this->collection    = new DeceasedCollection([$this->entityA]);
    }

    public function testItReturnsEntityClass(): void
    {
        $this->assertSame(Deceased::class, $this->collection->getSupportedEntityClass());
    }

    protected function getClosureForCollectionFiltering(): \Closure
    {
        return function (Deceased $deceased) {
            return $deceased->getDeathCertificateId() !== null;
        };
    }
}
