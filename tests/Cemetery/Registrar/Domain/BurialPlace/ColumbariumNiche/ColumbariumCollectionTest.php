<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\Columbarium;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumCollection;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\DataFixtures\BurialPlace\ColumbariumNiche\ColumbariumProvider;
use Cemetery\Tests\Registrar\Domain\EntityCollectionTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumCollectionTest extends EntityCollectionTest
{
    public function setUp(): void
    {
        $this->entityA    = ColumbariumProvider::getColumbariumA();
        $this->entityB    = ColumbariumProvider::getColumbariumB();
        $this->entityC    = ColumbariumProvider::getColumbariumC();
        $this->entityD    = ColumbariumProvider::getColumbariumD();
        $this->idA        = $this->entityA->id();
        $this->idB        = $this->entityB->id();
        $this->idC        = $this->entityC->id();
        $this->idD        = $this->entityD->id();
        $this->collection = new ColumbariumCollection([$this->entityA]);
    }

    public function testItReturnsEntityClassName(): void
    {
        $this->assertSame(Columbarium::class, $this->collection->supportedEntityClassName());
    }

    protected function getClosureForCollectionFiltering(): \Closure
    {
        return function (Columbarium $columbarium) {
            return $columbarium->geoPosition() !== null;
        };
    }
}
