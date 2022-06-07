<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorCollection;
use Cemetery\Tests\Registrar\Domain\EntityCollectionTest;
use DataFixtures\Organization\SoleProprietor\SoleProprietorProvider;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SoleProprietorCollectionTest extends EntityCollectionTest
{
    public function setUp(): void
    {
        $this->entityA    = SoleProprietorProvider::getSoleProprietorA();
        $this->entityB    = SoleProprietorProvider::getSoleProprietorB();
        $this->entityC    = SoleProprietorProvider::getSoleProprietorC();
        $this->entityD    = SoleProprietorProvider::getSoleProprietorD();
        $this->idA        = $this->entityA->id();
        $this->idB        = $this->entityB->id();
        $this->idC        = $this->entityC->id();
        $this->idD        = $this->entityD->id();
        $this->collection = new SoleProprietorCollection([$this->entityA]);
    }

    public function testItReturnsEntityClassName(): void
    {
        $this->assertSame(SoleProprietor::class, $this->collection->supportedEntityClassName());
    }

    protected function getClosureForCollectionFiltering(): \Closure
    {
        return function (SoleProprietor $soleProprietor) {
            return $soleProprietor->inn() !== null;
        };
    }
}
