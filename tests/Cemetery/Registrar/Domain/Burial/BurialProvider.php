<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialCode;
use Cemetery\Registrar\Domain\Burial\BurialContainerId;
use Cemetery\Registrar\Domain\Burial\BurialContainerType;
use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Registrar\Domain\Burial\BurialPlaceId;
use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNicheId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSiteId;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTreeId;
use Cemetery\Registrar\Domain\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;

final class BurialProvider
{
    public static function getBurialA(): Burial
    {
        $id            = new BurialId('B001');
        $burialCode    = new BurialCode('BC001');
        $deceasedId    = new DeceasedId('D001');
        $customerId    = new CustomerId(new NaturalPersonId('NP001'));
        $burialPlaceId = new BurialPlaceId(new ColumbariumNicheId('CN001'));
        $buriedAt      = new \DateTimeImmutable('2022-01-15 13:10:00');

        return (new Burial($id, $burialCode, $deceasedId))
            ->setCustomerId($customerId)
            ->setBurialPlaceId($burialPlaceId)
            ->setBuriedAt($buriedAt);
    }

    public static function getBurialB(): Burial
    {
        $id                 = new BurialId('B002');
        $burialCode         = new BurialCode('BC002');
        $deceasedId         = new DeceasedId('D002');
        $customerId         = new CustomerId(new NaturalPersonId('NP001'));
        $burialPlaceId      = new BurialPlaceId(new GraveSiteId('GS001'));
        $burialPlaceOwnerId = new NaturalPersonId('NP001');
        $burialContainerId  = new BurialContainerId('CT001', BurialContainerType::coffin());

        return (new Burial($id, $burialCode, $deceasedId))
            ->setCustomerId($customerId)
            ->setBurialPlaceId($burialPlaceId)
            ->setBurialPlaceOwnerId($burialPlaceOwnerId)
            ->setBurialContainerId($burialContainerId);
    }

    public static function getBurialC(): Burial
    {
        $id                 = new BurialId('B003');
        $burialCode         = new BurialCode('BC003');
        $deceasedId         = new DeceasedId('D003');
        $customerId         = new CustomerId(new NaturalPersonId('NP001'));
        $burialPlaceId      = new BurialPlaceId(new MemorialTreeId('MT001'));
        $burialPlaceOwnerId = new NaturalPersonId('NP002');
        $funeralCompanyId   = new FuneralCompanyId(new JuristicPersonId('JP001'));
        $burialContainerId  = new BurialContainerId('CT002', BurialContainerType::coffin());

        return (new Burial($id, $burialCode, $deceasedId))
            ->setCustomerId($customerId)
            ->setBurialPlaceId($burialPlaceId)
            ->setBurialPlaceOwnerId($burialPlaceOwnerId)
            ->setFuneralCompanyId($funeralCompanyId)
            ->setBurialContainerId($burialContainerId);
    }

    public static function getBurialD(): Burial
    {
        $id               = new BurialId('B004');
        $burialCode       = new BurialCode('BC004');
        $deceasedId       = new DeceasedId('D004');
        $funeralCompanyId = new FuneralCompanyId(new JuristicPersonId('JP001'));

        return (new Burial($id, $burialCode, $deceasedId))
            ->setFuneralCompanyId($funeralCompanyId);
    }
}
