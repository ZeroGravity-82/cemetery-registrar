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
use Cemetery\Registrar\Domain\Model\Burial\BurialType;
use Cemetery\Registrar\Domain\Model\Burial\CustomerId;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;
use DataFixtures\BurialPlace\ColumbariumNiche\ColumbariumNicheProvider;
use DataFixtures\BurialPlace\GraveSite\GraveSiteProvider;
use DataFixtures\BurialPlace\MemorialTree\MemorialTreeProvider;

class BurialProvider
{
    public static function getBurialA(): Burial
    {
        $id              = new BurialId('B001');
        $burialCode      = new BurialCode('11');
        $burialType      = BurialType::urnInColumbariumNiche();
        $deceasedId      = new NaturalPersonId('NP001');
        $customerId      = new CustomerId(new NaturalPersonId('NP005'));
        $burialPlace     = ColumbariumNicheProvider::getColumbariumNicheB();
        $buriedAt        = new \DateTimeImmutable('2021-12-03 13:10:00');
        $burialContainer = new BurialContainer(new Urn());

        return (new Burial($id, $burialCode, $burialType, $deceasedId))
            ->setCustomerId($customerId)
            ->setBurialPlace($burialPlace)
            ->setBurialContainer($burialContainer)
            ->setBuriedAt($buriedAt);
    }

    public static function getBurialB(): Burial
    {
        $id               = new BurialId('B002');
        $burialCode       = new BurialCode('11002');
        $burialType       = BurialType::coffinInGraveSite();
        $deceasedId       = new NaturalPersonId('NP002');
        $customerId       = new CustomerId(new NaturalPersonId('NP005'));
        $burialPlace      = GraveSiteProvider::getGraveSiteC();
        $burialContainer  = new BurialContainer(new Coffin(new CoffinSize(180), CoffinShape::trapezoid(), false));

        return (new Burial($id, $burialCode, $burialType, $deceasedId))
            ->setCustomerId($customerId)
            ->setBurialPlace($burialPlace)
            ->setBurialContainer($burialContainer);
    }

    public static function getBurialC(): Burial
    {
        $id               = new BurialId('B003');
        $burialCode       = new BurialCode('11003');
        $burialType       = BurialType::ashesUnderMemorialTree();
        $deceasedId       = new NaturalPersonId('NP003');
        $customerId       = new CustomerId(new NaturalPersonId('NP006'));
        $burialPlace      = MemorialTreeProvider::getMemorialTreeB();

        return (new Burial($id, $burialCode, $burialType, $deceasedId))
            ->setCustomerId($customerId)
            ->setBurialPlace($burialPlace);

    }

    public static function getBurialD(): Burial
    {
        $id               = new BurialId('B004');
        $burialCode       = new BurialCode('234117890');
        $burialType       = BurialType::coffinInGraveSite();
        $deceasedId       = new NaturalPersonId('NP005');
        $customerId       = new CustomerId(new JuristicPersonId('JP004'));
        $burialPlace      = GraveSiteProvider::getGraveSiteA();
        $funeralCompanyId = new FuneralCompanyId('FC001');

        return (new Burial($id, $burialCode, $burialType, $deceasedId))
            ->setCustomerId($customerId)
            ->setBurialPlace($burialPlace)
            ->setFuneralCompanyId($funeralCompanyId);
    }

    public static function getBurialE(): Burial
    {
        $id               = new BurialId('B005');
        $burialCode       = new BurialCode('11005');
        $burialType       = BurialType::coffinInGraveSite();
        $deceasedId       = new NaturalPersonId('NP004');
        $customerId       = new CustomerId(new SoleProprietorId('SP003'));
        $burialPlace      = GraveSiteProvider::getGraveSiteB();
        $funeralCompanyId = new FuneralCompanyId('FC002');
        $buriedAt         = new \DateTimeImmutable('2010-01-28 12:55:00');

        return (new Burial($id, $burialCode, $burialType, $deceasedId))
            ->setCustomerId($customerId)
            ->setBurialPlace($burialPlace)
            ->setFuneralCompanyId($funeralCompanyId)
            ->setBuriedAt($buriedAt);
    }

    public static function getBurialF(): Burial
    {
        $id               = new BurialId('B006');
        $burialCode       = new BurialCode('11006');
        $burialType       = BurialType::coffinInGraveSite();
        $deceasedId       = new NaturalPersonId('NP006');
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
        $deceasedId       = new NaturalPersonId('NP009');
        $burialPlace      = GraveSiteProvider::getGraveSiteE();
        $funeralCompanyId = new FuneralCompanyId('FC001');

        return (new Burial($id, $burialCode, $burialType, $deceasedId))
            ->setBurialPlace($burialPlace)
            ->setFuneralCompanyId($funeralCompanyId);
    }
}
