<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\Burial;
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
        $naturalPersonIdA = new NaturalPersonId('NP002');
        $naturalPersonIdB = new NaturalPersonId('NP003');
        $naturalPersonIdC = new NaturalPersonId('NP004');
        $naturalPersonIdD = new NaturalPersonId('NP005');
        $customerId       = new CustomerId('C001', CustomerType::naturalPerson());
        $siteIdA       = new SiteId('S001');
        $siteIdB       = new SiteId('S002');
        $siteIdC       = new SiteId('S003');
        $siteIdD       = new SiteId('S004');
        $this->idA     = new BurialId('B001');
        $this->idB     = new BurialId('B002');
        $this->idC     = new BurialId('B003');
        $this->idD     = new BurialId('B004');
        $this->entityA = new Burial($this->idA, $naturalPersonIdA, $customerId, $siteIdA, null);
        $this->entityB = new Burial($this->idB, $naturalPersonIdB, $customerId, $siteIdB, $naturalPersonIdB);
        $this->entityC = new Burial($this->idC, $naturalPersonIdC, $customerId, $siteIdC, $naturalPersonIdC);
        $this->entityD = new Burial($this->idD, $naturalPersonIdD, $customerId, $siteIdD, null);
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

