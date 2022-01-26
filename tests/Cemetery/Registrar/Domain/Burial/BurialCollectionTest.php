<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialCode;
use Cemetery\Registrar\Domain\Burial\BurialCollection;
use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Registrar\Domain\Burial\BurialPlaceId;
use Cemetery\Registrar\Domain\Burial\BurialPlaceType;
use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\Burial\CustomerType;
use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
use Cemetery\Registrar\Domain\Burial\FuneralCompanyType;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Tests\Registrar\Domain\AbstractEntityCollectionTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialCollectionTest extends AbstractEntityCollectionTest
{
    public function setUp(): void
    {
        $this->idA         = new BurialId('B001');
        $this->idB         = new BurialId('B002');
        $this->idC         = new BurialId('B003');
        $this->idD         = new BurialId('B004');
        $burialCodeA       = new BurialCode('BC001');
        $burialCodeB       = new BurialCode('BC002');
        $burialCodeC       = new BurialCode('BC003');
        $burialCodeD       = new BurialCode('BC004');
        $naturalPersonIdA  = new NaturalPersonId('NP001');
        $naturalPersonIdB  = new NaturalPersonId('NP002');
        $naturalPersonIdC  = new NaturalPersonId('NP003');
        $naturalPersonIdD  = new NaturalPersonId('NP004');
        $customerId        = new CustomerId('C001', CustomerType::naturalPerson());
        $siteIdA           = new BurialPlaceId('BP001', BurialPlaceType::columbariumNiche());
        $siteIdB           = new BurialPlaceId('BP002', BurialPlaceType::graveSite());
        $siteIdC           = new BurialPlaceId('BP003', BurialPlaceType::memorialTree());
        $funeralCompanyIdC = new FuneralCompanyId('FC001', FuneralCompanyType::soleProprietor());
        $this->entityA     = new Burial($this->idA, $burialCodeA, $naturalPersonIdA, $customerId, $siteIdA, null, null);
        $this->entityB     = new Burial($this->idB, $burialCodeB, $naturalPersonIdB, $customerId, $siteIdB, $naturalPersonIdB, null);
        $this->entityC     = new Burial($this->idC, $burialCodeC, $naturalPersonIdC, $customerId, $siteIdC, $naturalPersonIdC, $funeralCompanyIdC);
        $this->entityD     = new Burial($this->idD, $burialCodeD, $naturalPersonIdD, $customerId, null, null, null);
        $this->collection  = new BurialCollection([$this->entityA]);
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

