<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialPlace\MemorialTree;

use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTree;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeCollection;
use Cemetery\Tests\Registrar\Domain\AbstractEntityCollectionTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class MemorialTreeCollectionTest extends AbstractEntityCollectionTest
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
        return function (MemorialTree $deceased) {
            return $deceased->geoPosition() !== null;
        };
    }
}
