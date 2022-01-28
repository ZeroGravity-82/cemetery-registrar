<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialCollection;
use Cemetery\Tests\Registrar\Domain\AbstractEntityCollectionTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialCollectionTest extends AbstractEntityCollectionTest
{
    public function setUp(): void
    {
        $this->entityA    = BurialProvider::getBurialA();
        $this->entityB    = BurialProvider::getBurialB();
        $this->entityC    = BurialProvider::getBurialC();
        $this->entityD    = BurialProvider::getBurialD();
        $this->idA        = $this->entityA->getId();
        $this->idB        = $this->entityB->getId();
        $this->idC        = $this->entityC->getId();
        $this->idD        = $this->entityD->getId();
        $this->collection = new BurialCollection([$this->entityA]);
    }

    public function testItReturnsEntityClass(): void
    {
        $this->assertSame(Burial::class, $this->collection->getSupportedEntityClass());
    }

    protected function getClosureForCollectionFiltering(): \Closure
    {
        return function (Burial $burial) {
            return $burial->getBurialPlaceOwnerId() !== null;
        };
    }
}
