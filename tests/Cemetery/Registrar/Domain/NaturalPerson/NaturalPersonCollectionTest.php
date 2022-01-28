<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\NaturalPerson;

use Cemetery\Registrar\Domain\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonCollection;
use Cemetery\Tests\Registrar\Domain\AbstractEntityCollectionTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonCollectionTest extends AbstractEntityCollectionTest
{
    public function setUp(): void
    {
        $this->entityA    = NaturalPersonProvider::getNaturalPersonA();
        $this->entityB    = NaturalPersonProvider::getNaturalPersonB();
        $this->entityC    = NaturalPersonProvider::getNaturalPersonC();
        $this->entityD    = NaturalPersonProvider::getNaturalPersonD();
        $this->idA        = $this->entityA->getId();
        $this->idB        = $this->entityB->getId();
        $this->idC        = $this->entityC->getId();
        $this->idD        = $this->entityD->getId();
        $this->collection = new NaturalPersonCollection([$this->entityA]);
    }

    public function testItReturnsEntityClass(): void
    {
        $this->assertSame(NaturalPerson::class, $this->collection->getSupportedEntityClass());
    }

    protected function getClosureForCollectionFiltering(): \Closure
    {
        return function (NaturalPerson $naturalPerson) {
            return $naturalPerson->getBornAt() !== null;
        };
    }
}
