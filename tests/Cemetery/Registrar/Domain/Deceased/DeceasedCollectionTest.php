<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Deceased;

use Cemetery\Registrar\Domain\Deceased\Deceased;
use Cemetery\Registrar\Domain\Deceased\DeceasedCollection;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\DataFixtures\Deceased\DeceasedProvider;
use Cemetery\Tests\Registrar\Domain\EntityCollectionTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedCollectionTest extends EntityCollectionTest
{
    public function setUp(): void
    {
        $this->entityA    = DeceasedProvider::getDeceasedA();
        $this->entityB    = DeceasedProvider::getDeceasedB();
        $this->entityC    = DeceasedProvider::getDeceasedC();
        $this->entityD    = DeceasedProvider::getDeceasedD();
        $this->idA        = $this->entityA->id();
        $this->idB        = $this->entityB->id();
        $this->idC        = $this->entityC->id();
        $this->idD        = $this->entityD->id();
        $this->collection = new DeceasedCollection([$this->entityA]);
    }

    public function testItReturnsEntityClassName(): void
    {
        $this->assertSame(Deceased::class, $this->collection->supportedEntityClassName());
    }

    protected function getClosureForCollectionFiltering(): \Closure
    {
        return function (Deceased $deceased) {
            return $deceased->deathCertificateId() !== null;
        };
    }
}
