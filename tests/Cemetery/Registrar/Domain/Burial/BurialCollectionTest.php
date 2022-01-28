<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialCode;
use Cemetery\Registrar\Domain\Burial\BurialCollection;
use Cemetery\Registrar\Domain\Burial\BurialContainerId;
use Cemetery\Registrar\Domain\Burial\BurialContainerType;
use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Registrar\Domain\Burial\BurialPlaceId;
use Cemetery\Registrar\Domain\Burial\BurialPlaceType;
use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\Burial\CustomerType;
use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
use Cemetery\Registrar\Domain\Burial\FuneralCompanyType;
use Cemetery\Registrar\Domain\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Tests\Registrar\Domain\AbstractEntityCollectionTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialCollectionTest extends AbstractEntityCollectionTest
{
    public function setUp(): void
    {
        $this->idA           = new BurialId('B001');
        $this->idB           = new BurialId('B002');
        $this->idC           = new BurialId('B003');
        $this->idD           = new BurialId('B004');
        $burialCodeA         = new BurialCode('BC001');
        $burialCodeB         = new BurialCode('BC002');
        $burialCodeC         = new BurialCode('BC003');
        $burialCodeD         = new BurialCode('BC004');
        $deceasedIdA         = new DeceasedId('D001');
        $deceasedIdB         = new DeceasedId('D002');
        $deceasedIdC         = new DeceasedId('D003');
        $deceasedIdD         = new DeceasedId('D004');
        $customerId          = new CustomerId('C001', CustomerType::naturalPerson());
        $burialPlaceIdA      = new BurialPlaceId('BP001', BurialPlaceType::columbariumNiche());
        $burialPlaceIdB      = new BurialPlaceId('BP002', BurialPlaceType::graveSite());
        $burialPlaceIdC      = new BurialPlaceId('BP003', BurialPlaceType::memorialTree());
        $burialPlaceOwnerIdB = new NaturalPersonId('NP001');
        $burialPlaceOwnerIdC = new NaturalPersonId('NP002');
        $funeralCompanyIdC   = new FuneralCompanyId('FC001', FuneralCompanyType::soleProprietor());
        $burialContainerIdB  = new BurialContainerId('CT001', BurialContainerType::coffin());
        $burialContainerIdC  = new BurialContainerId('CT002', BurialContainerType::coffin());
        $buriedAtA           = new \DateTimeImmutable('2022-01-15 13:10:00');
        $this->entityA       = new Burial($this->idA, $burialCodeA, $deceasedIdA, $customerId, $burialPlaceIdA, null, null, null, $buriedAtA);
        $this->entityB       = new Burial($this->idB, $burialCodeB, $deceasedIdB, $customerId, $burialPlaceIdB, $burialPlaceOwnerIdB, null, $burialContainerIdB, null);
        $this->entityC       = new Burial($this->idC, $burialCodeC, $deceasedIdC, $customerId, $burialPlaceIdC, $burialPlaceOwnerIdC, $funeralCompanyIdC, $burialContainerIdC, null);
        $this->entityD       = new Burial($this->idD, $burialCodeD, $deceasedIdD, $customerId, null, null, null, null, null);
        $this->collection    = new BurialCollection([$this->entityA]);
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
