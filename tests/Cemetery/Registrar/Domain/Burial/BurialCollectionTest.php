<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialCode;
use Cemetery\Registrar\Domain\Burial\BurialCollection;
use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\Burial\CustomerType;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Site\SiteId;
use Cemetery\Tests\Registrar\Domain\AbstractEntityCollectionTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialCollectionTest extends AbstractEntityCollectionTest
{
    public function setUp(): void
    {
        $this->idA        = new BurialId('B001');
        $this->idB        = new BurialId('B002');
        $this->idC        = new BurialId('B003');
        $this->idD        = new BurialId('B004');
        $burialCodeA      = new BurialCode('BC001');
        $burialCodeB      = new BurialCode('BC002');
        $burialCodeC      = new BurialCode('BC003');
        $burialCodeD      = new BurialCode('BC004');
        $naturalPersonIdA = new NaturalPersonId('NP001');
        $naturalPersonIdB = new NaturalPersonId('NP002');
        $naturalPersonIdC = new NaturalPersonId('NP003');
        $naturalPersonIdD = new NaturalPersonId('NP004');
        $customerId       = new CustomerId('C001', CustomerType::naturalPerson());
        $siteIdA          = new SiteId('S001');
        $siteIdB          = new SiteId('S002');
        $siteIdC          = new SiteId('S003');
        $siteIdD          = new SiteId('S004');
        $this->entityA    = new Burial($this->idA, $burialCodeA, $naturalPersonIdA, $siteIdA, $customerId, null);
        $this->entityB    = new Burial($this->idB, $burialCodeB, $naturalPersonIdB, $siteIdB, $customerId, $naturalPersonIdB);
        $this->entityC    = new Burial($this->idC, $burialCodeC, $naturalPersonIdC, $siteIdC, $customerId, $naturalPersonIdC);
        $this->entityD    = new Burial($this->idD, $burialCodeD, $naturalPersonIdD, $siteIdD, $customerId, null);
        $this->collection = new BurialCollection([$this->entityA]);
    }

    public function testItReturnsEntityClass(): void
    {
        $this->assertSame(Burial::class, $this->collection->getSupportedEntityClass());
    }

    protected function getClosureForCollectionFiltering(): \Closure
    {
        return function (Burial $burial) {
            return $burial->getSiteOwnerId() !== null;
        };
    }
}

