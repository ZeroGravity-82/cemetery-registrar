<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialCode;
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

final class BurialProvider
{
    public static function getBurialA(): Burial
    {
        $id            = new BurialId('B001');
        $burialCode    = new BurialCode('BC001');
        $deceasedId    = new DeceasedId('D001');
        $customerId    = new CustomerId('C001', CustomerType::naturalPerson());
        $burialPlaceId = new BurialPlaceId('BP001', BurialPlaceType::columbariumNiche());
        $buriedAt      = new \DateTimeImmutable('2022-01-15 13:10:00');

        return new Burial($id, $burialCode, $deceasedId, $customerId, $burialPlaceId, null, null, null, $buriedAt);
    }

    public static function getBurialB(): Burial
    {
        $id                 = new BurialId('B002');
        $burialCode         = new BurialCode('BC002');
        $deceasedId         = new DeceasedId('D002');
        $customerId         = new CustomerId('C001', CustomerType::naturalPerson());
        $burialPlaceId      = new BurialPlaceId('BP002', BurialPlaceType::graveSite());
        $burialPlaceOwnerId = new NaturalPersonId('NP001');
        $burialContainerId  = new BurialContainerId('CT001', BurialContainerType::coffin());

        return new Burial($id, $burialCode, $deceasedId, $customerId, $burialPlaceId, $burialPlaceOwnerId, null, $burialContainerId, null);
    }

    public static function getBurialC(): Burial
    {
        $id                 = new BurialId('B003');
        $burialCode         = new BurialCode('BC003');
        $deceasedId         = new DeceasedId('D003');
        $customerId         = new CustomerId('C001', CustomerType::naturalPerson());
        $burialPlaceId      = new BurialPlaceId('BP003', BurialPlaceType::memorialTree());
        $burialPlaceOwnerId = new NaturalPersonId('NP002');
        $funeralCompanyId   = new FuneralCompanyId('FC001', FuneralCompanyType::soleProprietor());
        $burialContainerId  = new BurialContainerId('CT002', BurialContainerType::coffin());

        return new Burial($id, $burialCode, $deceasedId, $customerId, $burialPlaceId, $burialPlaceOwnerId, $funeralCompanyId, $burialContainerId, null);
    }

    public static function getBurialD(): Burial
    {
        $id         = new BurialId('B004');
        $burialCode = new BurialCode('BC004');
        $deceasedId = new DeceasedId('D004');
        $customerId = new CustomerId('C001', CustomerType::naturalPerson());

        return new Burial($id, $burialCode, $deceasedId, $customerId, null, null, null, null, null);
    }
}
