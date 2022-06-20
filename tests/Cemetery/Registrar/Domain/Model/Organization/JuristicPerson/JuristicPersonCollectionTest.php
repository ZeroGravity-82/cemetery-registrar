<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonCollection;
use Cemetery\Tests\Registrar\Domain\Model\AggregateRootCollectionTest;
use DataFixtures\Organization\JuristicPerson\JuristicPersonProvider;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonCollectionTest extends AggregateRootCollectionTest
{
    public function setUp(): void
    {
        $this->entityA    = JuristicPersonProvider::getJuristicPersonA();
        $this->entityB    = JuristicPersonProvider::getJuristicPersonB();
        $this->entityC    = JuristicPersonProvider::getJuristicPersonC();
        $this->entityD    = JuristicPersonProvider::getJuristicPersonD();
        $this->idA        = $this->entityA->id();
        $this->idB        = $this->entityB->id();
        $this->idC        = $this->entityC->id();
        $this->idD        = $this->entityD->id();
        $this->collection = new JuristicPersonCollection([$this->entityA]);
    }

    public function testItReturnsSupportedEntityClassName(): void
    {
        $this->assertSame(JuristicPerson::class, $this->collection->supportedEntityClassName());
    }

    protected function getClosureForCollectionFiltering(): \Closure
    {
        return function (JuristicPerson $juristicPerson) {
            return $juristicPerson->inn() !== null;
        };
    }
}
