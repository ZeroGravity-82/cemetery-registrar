<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonCollection;
use Cemetery\Tests\Registrar\Domain\Model\EntityCollectionTest;
use DataFixtures\NaturalPerson\NaturalPersonProvider;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonCollectionTest extends EntityCollectionTest
{
    public function setUp(): void
    {
        $this->entityA    = NaturalPersonProvider::getNaturalPersonA();
        $this->entityB    = NaturalPersonProvider::getNaturalPersonB();
        $this->entityC    = NaturalPersonProvider::getNaturalPersonC();
        $this->entityD    = NaturalPersonProvider::getNaturalPersonD();
        $this->idA        = $this->entityA->id();
        $this->idB        = $this->entityB->id();
        $this->idC        = $this->entityC->id();
        $this->idD        = $this->entityD->id();
        $this->collection = new NaturalPersonCollection([$this->entityA]);
    }

    public function testItReturnsEntityClassName(): void
    {
        $this->assertSame(NaturalPerson::class, $this->collection->supportedEntityClassName());
    }

    protected function getClosureForCollectionFiltering(): \Closure
    {
        return function (NaturalPerson $naturalPerson) {
            return $naturalPerson->bornAt() !== null;
        };
    }
}
