<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Deceased\Deceased;
use Cemetery\Registrar\Domain\Deceased\DeceasedCollection;
use Cemetery\Registrar\Domain\Deceased\DeceasedId;
use Cemetery\Tests\Registrar\Domain\AbstractEntityCollectionTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialCollectionTest extends AbstractEntityCollectionTest
{
    public function setUp(): void
    {
        $this->idA          = new DeceasedId('D001');
        $this->idB          = new DeceasedId('D002');
        $this->idC          = new DeceasedId('D003');
        $this->idD          = new DeceasedId('D004');


        $this->entityA      = new Deceased($this->idA, $burialCodeA, $naturalPersonIdA, $customerId, $siteIdA, null, null, null);
        $this->entityB      = new Deceased($this->idB, $burialCodeB, $naturalPersonIdB, $customerId, $siteIdB, $naturalPersonIdB, null, $burialContainerIdB);
        $this->entityC      = new Deceased($this->idC, $burialCodeC, $naturalPersonIdC, $customerId, $siteIdC, $naturalPersonIdC, $funeralCompanyIdC, $burialContainerIdC);
        $this->entityD      = new Deceased($this->idD, $burialCodeD, $naturalPersonIdD, $customerId, null, null, null, null);
        $this->collection   = new DeceasedCollection([$this->entityA]);
    }

    public function testItReturnsEntityClass(): void
    {
        $this->assertSame(Deceased::class, $this->collection->getSupportedEntityClass());
    }

    protected function getClosureForCollectionFiltering(): \Closure
    {
        return function (Deceased $burial) {
            return $burial->getBurialPlaceOwnerId() !== null;
        };
    }
}
