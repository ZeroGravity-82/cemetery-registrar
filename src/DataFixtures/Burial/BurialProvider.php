<?php

declare(strict_types=1);

namespace DataFixtures\Burial;

use Cemetery\Registrar\Domain\Model\Burial\Burial;
use Cemetery\Registrar\Domain\Model\Burial\BurialCode;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\Coffin;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\CoffinShape;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\CoffinSize;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\Urn;
use Cemetery\Registrar\Domain\Model\Burial\BurialId;
use Cemetery\Registrar\Domain\Model\Burial\BurialPlaceId;
use Cemetery\Registrar\Domain\Model\Burial\BurialType;
use Cemetery\Registrar\Domain\Model\Burial\CustomerId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Domain\Model\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;

class BurialProvider
{
    public static function getBurialA(): Burial
    {
        $id              = new BurialId('B001');
        $burialCode      = new BurialCode('11');
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
        $id               = new BurialId('B002');
        $burialCode       = new BurialCode('11002');
        $burialType       = BurialType::coffinInGraveSite();
        $deceasedId       = new DeceasedId('D002');
        $customerId       = new CustomerId(new NaturalPersonId('NP005'));
        $burialPlaceId    = new BurialPlaceId(new GraveSiteId('GS003'));
        $personInChargeId = new NaturalPersonId('NP006');
        $burialContainer  = new BurialContainer(new Coffin(new CoffinSize(180), CoffinShape::trapezoid(), false));

        return (new Burial($id, $burialCode, $burialType, $deceasedId))
            ->setCustomerId($customerId)
            ->setBurialPlaceId($burialPlaceId)
            ->setPersonInChargeId($personInChargeId)
            ->setBurialContainer($burialContainer);
    }

    public static function getBurialC(): Burial
    {
        $id               = new BurialId('B003');
        $burialCode       = new BurialCode('11003');
        $burialType       = BurialType::ashesUnderMemorialTree();
        $deceasedId       = new DeceasedId('D003');
        $customerId       = new CustomerId(new NaturalPersonId('NP006'));
        $burialPlaceId    = new BurialPlaceId(new MemorialTreeId('MT002'));
        $personInChargeId = new NaturalPersonId('NP006');

        return (new Burial($id, $burialCode, $burialType, $deceasedId))
            ->setCustomerId($customerId)
            ->setBurialPlaceId($burialPlaceId)
            ->setPersonInChargeId($personInChargeId);
    }

    public static function getBurialD(): Burial
    {
        $id               = new BurialId('B004');
        $burialCode       = new BurialCode('234117890');
        $burialType       = BurialType::coffinInGraveSite();
        $deceasedId       = new DeceasedId('D004');
        $customerId       = new CustomerId(new JuristicPersonId('JP004'));
        $burialPlaceId    = new BurialPlaceId(new GraveSiteId('GS001'));
        $funeralCompanyId = new FuneralCompanyId('FC001');

        return (new Burial($id, $burialCode, $burialType, $deceasedId))
            ->setCustomerId($customerId)
            ->setBurialPlaceId($burialPlaceId)
            ->setFuneralCompanyId($funeralCompanyId);
    }

    public static function getBurialE(): Burial
    {
        $id               = new BurialId('B005');
        $burialCode       = new BurialCode('11005');
        $burialType       = BurialType::coffinInGraveSite();
        $deceasedId       = new DeceasedId('D005');
        $customerId       = new CustomerId(new SoleProprietorId('SP003'));
        $burialPlaceId    = new BurialPlaceId(new GraveSiteId('GS002'));
        $personInChargeId = new NaturalPersonId('NP008');
        $funeralCompanyId = new FuneralCompanyId('FC002');
        $buriedAt         = new \DateTimeImmutable('2010-01-28 12:55:00');

        return (new Burial($id, $burialCode, $burialType, $deceasedId))
            ->setCustomerId($customerId)
            ->setBurialPlaceId($burialPlaceId)
            ->setPersonInChargeId($personInChargeId)
            ->setFuneralCompanyId($funeralCompanyId)
            ->setBuriedAt($buriedAt);
    }

    public static function getBurialF(): Burial
    {
        $id               = new BurialId('B006');
        $burialCode       = new BurialCode('11006');
        $burialType       = BurialType::coffinInGraveSite();
        $deceasedId       = new DeceasedId('D006');
        $customerId       = new CustomerId(new NaturalPersonId('NP007'));
        $funeralCompanyId = new FuneralCompanyId('FC003');

        return (new Burial($id, $burialCode, $burialType, $deceasedId))
            ->setCustomerId($customerId)
            ->setFuneralCompanyId($funeralCompanyId);
    }

    public static function getBurialG(): Burial
    {
        $id               = new BurialId('B007');
        $burialCode       = new BurialCode('1');
        $burialType       = BurialType::coffinInGraveSite();
        $deceasedId       = new DeceasedId('D007');
        $burialPlaceId    = new BurialPlaceId(new GraveSiteId('GS005'));
        $funeralCompanyId = new FuneralCompanyId('FC001');

        return (new Burial($id, $burialCode, $burialType, $deceasedId))
            ->setBurialPlaceId($burialPlaceId)
            ->setFuneralCompanyId($funeralCompanyId);
    }
}
