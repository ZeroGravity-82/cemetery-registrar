<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Burial;

use Cemetery\Registrar\Domain\Model\Burial\Burial;
use Cemetery\Registrar\Domain\Model\Burial\BurialCollection;
use Cemetery\Tests\Registrar\Domain\Model\AggregateRootCollectionTest;
use DataFixtures\Burial\BurialProvider;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialCollectionTest extends AggregateRootCollectionTest
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

    public function testItReturnsSupportedEntityClassName(): void
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
