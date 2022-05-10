<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonCollection;
use Cemetery\Tests\Registrar\Domain\EntityCollectionTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonCollectionTest extends EntityCollectionTest
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

    public function testItReturnsEntityClassName(): void
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
