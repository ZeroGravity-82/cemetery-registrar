<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\Columbarium;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumCollection;
use Cemetery\Tests\Registrar\Domain\Model\EntityCollectionTest;
use DataFixtures\BurialPlace\ColumbariumNiche\ColumbariumProvider;

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

    public function testItReturnsSupportedEntityClassName(): void
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
