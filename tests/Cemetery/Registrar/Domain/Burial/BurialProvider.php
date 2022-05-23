<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialCode;
use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Registrar\Domain\Burial\BurialPlaceId;
use Cemetery\Registrar\Domain\Burial\BurialType;
use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
use Cemetery\Registrar\Domain\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\BurialContainer\Coffin;
use Cemetery\Registrar\Domain\BurialContainer\CoffinShape;
use Cemetery\Registrar\Domain\BurialContainer\CoffinSize;
use Cemetery\Registrar\Domain\BurialContainer\Urn;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Domain\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;

final class BurialProvider
{
    public static function getBurialA(): Burial
    {
        $id              = new BurialId('B001');
        $burialCode      = new BurialCode('000000001');
        $burialType      = BurialType::urnInColumbariumNiche();
        $deceasedId      = new DeceasedId('D001');
        $customerId      = new CustomerId(new NaturalPersonId('NP005'));
        $burialPlaceId   = new BurialPlaceId(new ColumbariumNicheId('CN002'));
        $buriedAt        = new \DateTimeImmutable('2021-12-03 13:10:00');
        $burialContainer = new BurialContainer(new Urn());

        return (new Burial($id, $burialCode, $burialType, $deceasedId))
            ->setCustomerId($customerId)
            ->setBurialPlaceId($burialPlaceId)
            ->setBurialContainer($burialContainer)
            ->setBuriedAt($buriedAt);
    }

    public static function getBurialB(): Burial
    {
        $id                 = new BurialId('B002');
        $burialCode         = new BurialCode('000000002');
        $burialType         = BurialType::coffinInGraveSite();
        $deceasedId         = new DeceasedId('D002');
        $customerId         = new CustomerId(new NaturalPersonId('NP005'));
        $burialPlaceId      = new BurialPlaceId(new GraveSiteId('GS003'));
        $burialPlaceOwnerId = new NaturalPersonId('NP006');
        $burialContainer    = new BurialContainer(new Coffin(new CoffinSize(180), CoffinShape::trapezoid(), false));

        return (new Burial($id, $burialCode, $burialType, $deceasedId))
            ->setCustomerId($customerId)
            ->setBurialPlaceId($burialPlaceId)
            ->setBurialPlaceOwnerId($burialPlaceOwnerId)
            ->setBurialContainer($burialContainer);
    }

    public static function getBurialC(): Burial
    {
        $id                 = new BurialId('B003');
        $burialCode         = new BurialCode('000000003');
        $burialType         = BurialType::ashesUnderMemorialTree();
        $deceasedId         = new DeceasedId('D003');
        $customerId         = new CustomerId(new NaturalPersonId('NP006'));
        $burialPlaceId      = new BurialPlaceId(new MemorialTreeId('MT002'));
        $burialPlaceOwnerId = new NaturalPersonId('NP006');
        $funeralCompanyId   = new FuneralCompanyId(new JuristicPersonId('JP001'));

        return (new Burial($id, $burialCode, $burialType, $deceasedId))
            ->setCustomerId($customerId)
            ->setBurialPlaceId($burialPlaceId)
            ->setBurialPlaceOwnerId($burialPlaceOwnerId)
            ->setFuneralCompanyId($funeralCompanyId);
    }

    public static function getBurialD(): Burial
    {
        $id               = new BurialId('B004');
        $burialCode       = new BurialCode('000000004');
        $burialType       = BurialType::coffinInGraveSite();
        $deceasedId       = new DeceasedId('D004');
        $customerId       = new CustomerId(new JuristicPersonId('JP004'));
        $burialPlaceId    = new BurialPlaceId(new GraveSiteId('GS001'));
        $funeralCompanyId = new FuneralCompanyId(new JuristicPersonId('JP001'));

        return (new Burial($id, $burialCode, $burialType, $deceasedId))
            ->setCustomerId($customerId)
            ->setBurialPlaceId($burialPlaceId)
            ->setFuneralCompanyId($funeralCompanyId);
    }

    public static function getBurialE(): Burial
    {
        $id                 = new BurialId('B005');
        $burialCode         = new BurialCode('000000005');
        $burialType         = BurialType::coffinInGraveSite();
        $deceasedId         = new DeceasedId('D005');
        $customerId         = new CustomerId(new SoleProprietorId('SP003'));
        $burialPlaceId      = new BurialPlaceId(new GraveSiteId('GS002'));
        $burialPlaceOwnerId = new NaturalPersonId('NP008');
        $funeralCompanyId   = new FuneralCompanyId(new JuristicPersonId('JP002'));
        $buriedAt           = new \DateTimeImmutable('2010-01-28 12:55:00');

        return (new Burial($id, $burialCode, $burialType, $deceasedId))
            ->setCustomerId($customerId)
            ->setBurialPlaceId($burialPlaceId)
            ->setBurialPlaceOwnerId($burialPlaceOwnerId)
            ->setFuneralCompanyId($funeralCompanyId)
            ->setBuriedAt($buriedAt);
    }

    public static function getBurialF(): Burial
    {
        $id               = new BurialId('B006');
        $burialCode       = new BurialCode('000000006');
        $burialType       = BurialType::coffinInGraveSite();
        $deceasedId       = new DeceasedId('D006');
        $customerId       = new CustomerId(new NaturalPersonId('NP007'));
        $funeralCompanyId = new FuneralCompanyId(new SoleProprietorId('SP002'));

        return (new Burial($id, $burialCode, $burialType, $deceasedId))
            ->setCustomerId($customerId)
            ->setFuneralCompanyId($funeralCompanyId);
    }
}
