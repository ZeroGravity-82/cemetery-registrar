<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorCollection;
use Cemetery\Tests\Registrar\Domain\AbstractEntityCollectionTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SoleProprietorCollectionTest extends AbstractEntityCollectionTest
{
    public function setUp(): void
    {
        $this->entityA    = SoleProprietorProvider::getSoleProprietorA();
        $this->entityB    = SoleProprietorProvider::getSoleProprietorB();
        $this->entityC    = SoleProprietorProvider::getSoleProprietorC();
        $this->entityD    = SoleProprietorProvider::getSoleProprietorD();
        $this->idA        = $this->entityA->getId();
        $this->idB        = $this->entityB->getId();
        $this->idC        = $this->entityC->getId();
        $this->idD        = $this->entityD->getId();
        $this->collection = new SoleProprietorCollection([$this->entityA]);
    }

    public function testItReturnsEntityClass(): void
    {
        $this->assertSame(SoleProprietor::class, $this->collection->getSupportedEntityClass());
    }

    protected function getClosureForCollectionFiltering(): \Closure
    {
        return function (SoleProprietor $soleProprietor) {
            return $soleProprietor->getInn() !== null;
        };
    }
}
