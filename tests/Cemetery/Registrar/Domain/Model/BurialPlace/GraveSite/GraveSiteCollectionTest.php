<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteCollection;
use Cemetery\Tests\Registrar\Domain\Model\AggregateRootCollectionTest;
use DataFixtures\BurialPlace\GraveSite\GraveSiteProvider;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteCollectionTest extends AggregateRootCollectionTest
{
    public function setUp(): void
    {
        $this->entityA    = GraveSiteProvider::getGraveSiteA();
        $this->entityB    = GraveSiteProvider::getGraveSiteB();
        $this->entityC    = GraveSiteProvider::getGraveSiteC();
        $this->entityD    = GraveSiteProvider::getGraveSiteD();
        $this->idA        = $this->entityA->id();
        $this->idB        = $this->entityB->id();
        $this->idC        = $this->entityC->id();
        $this->idD        = $this->entityD->id();
        $this->collection = new GraveSiteCollection([$this->entityA]);
    }

    public function testItReturnsSupportedEntityClassName(): void
    {
        $this->assertSame(GraveSite::class, $this->collection->supportedEntityClassName());
    }

    protected function getClosureForCollectionFiltering(): \Closure
    {
        return function (GraveSite $graveSite) {
            return $graveSite->geoPosition() !== null;
        };
    }
}
