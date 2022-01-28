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
        $this->entityA    = DeceasedProvider::getDeceasedA();
        $this->entityB    = DeceasedProvider::getDeceasedB();
        $this->entityC    = DeceasedProvider::getDeceasedC();
        $this->entityD    = DeceasedProvider::getDeceasedD();
        $this->idA        = $this->entityA->getId();
        $this->idB        = $this->entityB->getId();
        $this->idC        = $this->entityC->getId();
        $this->idD        = $this->entityD->getId();
        $this->collection = new DeceasedCollection([$this->entityA]);
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
