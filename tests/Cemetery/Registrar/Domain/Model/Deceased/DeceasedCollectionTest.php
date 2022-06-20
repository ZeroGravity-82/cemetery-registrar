<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Deceased;

use Cemetery\Registrar\Domain\Model\Deceased\Deceased;
use Cemetery\Registrar\Domain\Model\Deceased\DeceasedCollection;
use Cemetery\Tests\Registrar\Domain\Model\AggregateRootCollectionTest;
use DataFixtures\Deceased\DeceasedProvider;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedCollectionTest extends AggregateRootCollectionTest
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

    public function testItReturnsSupportedClassName(): void
    {
        $this->assertSame(Deceased::class, $this->collection->supportedClassName());
    }

    protected function getClosureForCollectionFiltering(): \Closure
    {
        return function (Deceased $deceased) {
            return $deceased->deathCertificateId() !== null;
        };
    }
}
