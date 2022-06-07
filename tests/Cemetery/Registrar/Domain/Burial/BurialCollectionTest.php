<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialCollection;
use Cemetery\Tests\Registrar\Domain\EntityCollectionTest;
use DataFixtures\Burial\BurialProvider;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialCollectionTest extends EntityCollectionTest
{
    public function setUp(): void
    {
        $this->entityA    = BurialProvider::getBurialA();
        $this->entityB    = BurialProvider::getBurialB();
        $this->entityC    = BurialProvider::getBurialC();
        $this->entityD    = BurialProvider::getBurialD();
        $this->idA        = $this->entityA->id();
        $this->idB        = $this->entityB->id();
        $this->idC        = $this->entityC->id();
        $this->idD        = $this->entityD->id();
        $this->collection = new BurialCollection([$this->entityA]);
    }

    public function testItReturnsEntityClassName(): void
    {
        $this->assertSame(Burial::class, $this->collection->supportedEntityClassName());
    }

    protected function getClosureForCollectionFiltering(): \Closure
    {
        return function (Burial $burial) {
            return $burial->burialPlaceOwnerId() !== null;
        };
    }
}
