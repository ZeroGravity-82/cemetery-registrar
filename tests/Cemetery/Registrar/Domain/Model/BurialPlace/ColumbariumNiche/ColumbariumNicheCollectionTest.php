<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheCollection;
use Cemetery\Tests\Registrar\Domain\Model\EntityCollectionTest;
use DataFixtures\BurialPlace\ColumbariumNiche\ColumbariumNicheProvider;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumNicheCollectionTest extends EntityCollectionTest
{
    public function setUp(): void
    {
        $this->entityA    = ColumbariumNicheProvider::getColumbariumNicheA();
        $this->entityB    = ColumbariumNicheProvider::getColumbariumNicheB();
        $this->entityC    = ColumbariumNicheProvider::getColumbariumNicheC();
        $this->entityD    = ColumbariumNicheProvider::getColumbariumNicheD();
        $this->idA        = $this->entityA->id();
        $this->idB        = $this->entityB->id();
        $this->idC        = $this->entityC->id();
        $this->idD        = $this->entityD->id();
        $this->collection = new ColumbariumNicheCollection([$this->entityA]);
    }

    public function testItReturnsEntityClassName(): void
    {
        $this->assertSame(ColumbariumNiche::class, $this->collection->supportedEntityClassName());
    }

    protected function getClosureForCollectionFiltering(): \Closure
    {
        return function (ColumbariumNiche $columbariumNiche) {
            return $columbariumNiche->geoPosition() !== null;
        };
    }
}
