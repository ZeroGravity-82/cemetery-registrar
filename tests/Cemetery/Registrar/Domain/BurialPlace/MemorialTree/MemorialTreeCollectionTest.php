<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialPlace\MemorialTree;

use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTree;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeCollection;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\DataFixtures\BurialPlace\MemorialTree\MemorialTreeProvider;
use Cemetery\Tests\Registrar\Domain\EntityCollectionTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class MemorialTreeCollectionTest extends EntityCollectionTest
{
    public function setUp(): void
    {
        $this->entityA    = MemorialTreeProvider::getMemorialTreeA();
        $this->entityB    = MemorialTreeProvider::getMemorialTreeB();
        $this->entityC    = MemorialTreeProvider::getMemorialTreeC();
        $this->entityD    = MemorialTreeProvider::getMemorialTreeD();
        $this->idA        = $this->entityA->id();
        $this->idB        = $this->entityB->id();
        $this->idC        = $this->entityC->id();
        $this->idD        = $this->entityD->id();
        $this->collection = new MemorialTreeCollection([$this->entityA]);
    }

    public function testItReturnsEntityClassName(): void
    {
        $this->assertSame(MemorialTree::class, $this->collection->supportedEntityClassName());
    }

    protected function getClosureForCollectionFiltering(): \Closure
    {
        return function (MemorialTree $memorialTree) {
            return $memorialTree->geoPosition() !== null;
        };
    }
}
