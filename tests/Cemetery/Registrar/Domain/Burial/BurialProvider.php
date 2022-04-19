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
use Cemetery\Registrar\Domain\BurialContainer\Coffin;
use Cemetery\Registrar\Domain\BurialContainer\CoffinShape;
use Cemetery\Registrar\Domain\BurialContainer\CoffinSize;
use Cemetery\Registrar\Domain\BurialContainer\Urn;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNicheId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSiteId;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTreeId;
use Cemetery\Registrar\Domain\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;

final class BurialProvider
{
    public static function getBurialA(): Burial
    {
        $id              = new BurialId('B001');
        $burialCode      = new BurialCode('BC001');
        $deceasedId      = new DeceasedId('D001');
        $burialType      = BurialType::urnInColumbariumNiche();
        $customerId      = new CustomerId(new NaturalPersonId('ID001'));
        $burialPlaceId   = new BurialPlaceId(new ColumbariumNicheId('CN001'));
        $buriedAt        = new \DateTimeImmutable('2022-01-15 13:10:00');
        $burialContainer = new Urn();

        return (new Burial($id, $burialCode, $deceasedId,$burialType))
            ->setCustomerId($customerId)
            ->setBurialPlaceId($burialPlaceId)
            ->setBurialContainer($burialContainer)
            ->setBuriedAt($buriedAt);
    }

    public static function getBurialB(): Burial
    {
        $id                 = new BurialId('B002');
        $burialCode         = new BurialCode('BC002');
        $deceasedId         = new DeceasedId('D002');
        $burialType         = BurialType::coffinInGraveSite();
        $customerId         = new CustomerId(new NaturalPersonId('ID001'));
        $burialPlaceId      = new BurialPlaceId(new GraveSiteId('GS001'));
        $burialPlaceOwnerId = new NaturalPersonId('ID001');
        $burialContainer    = new Coffin(new CoffinSize(180), CoffinShape::trapezoid(), false);

        return (new Burial($id, $burialCode, $deceasedId, $burialType))
            ->setCustomerId($customerId)
            ->setBurialPlaceId($burialPlaceId)
            ->setBurialPlaceOwnerId($burialPlaceOwnerId)
            ->setBurialContainer($burialContainer);
    }

    public static function getBurialC(): Burial
    {
        $id                 = new BurialId('B003');
        $burialCode         = new BurialCode('BC003');
        $deceasedId         = new DeceasedId('D003');
        $burialType         = BurialType::ashesUnderMemorialTree();
        $customerId         = new CustomerId(new NaturalPersonId('ID001'));
        $burialPlaceId      = new BurialPlaceId(new MemorialTreeId('MT001'));
        $burialPlaceOwnerId = new NaturalPersonId('ID003');
        $funeralCompanyId   = new FuneralCompanyId(new JuristicPersonId('ID001'));

        return (new Burial($id, $burialCode, $deceasedId, $burialType))
            ->setCustomerId($customerId)
            ->setBurialPlaceId($burialPlaceId)
            ->setBurialPlaceOwnerId($burialPlaceOwnerId)
            ->setFuneralCompanyId($funeralCompanyId);
    }

    public static function getBurialD(): Burial
    {
        $id               = new BurialId('B004');
        $burialCode       = new BurialCode('BC004');
        $deceasedId       = new DeceasedId('D004');
        $burialType       = BurialType::coffinInGraveSite();
        $funeralCompanyId = new FuneralCompanyId(new JuristicPersonId('ID001'));

        return (new Burial($id, $burialCode, $deceasedId, $burialType))
            ->setFuneralCompanyId($funeralCompanyId);
    }

    public static function getBurialE(): Burial
    {
        $id               = new BurialId('B005');
        $burialCode       = new BurialCode('BC005');
        $deceasedId       = new DeceasedId('D005');
        $burialType       = BurialType::coffinInGraveSite();
        $customerId       = new CustomerId(new SoleProprietorId('ID001'));
        $funeralCompanyId = new FuneralCompanyId(new JuristicPersonId('ID002'));

        return (new Burial($id, $burialCode, $deceasedId, $burialType))
            ->setCustomerId($customerId)
            ->setFuneralCompanyId($funeralCompanyId);
    }

    public static function getBurialF(): Burial
    {
        $id               = new BurialId('B006');
        $burialCode       = new BurialCode('BC006');
        $deceasedId       = new DeceasedId('D006');
        $burialType       = BurialType::coffinInGraveSite();
        $customerId       = new CustomerId(new NaturalPersonId('ID002'));
        $funeralCompanyId = new FuneralCompanyId(new SoleProprietorId('ID001'));

        return (new Burial($id, $burialCode, $deceasedId, $burialType))
            ->setCustomerId($customerId)
            ->setFuneralCompanyId($funeralCompanyId);
    }
}
