<?php

declare(strict_types=1);

namespace DataFixtures\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheNumber;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\RowInColumbarium;
use Cemetery\Registrar\Domain\Model\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\Model\GeoPosition\Error;
use Cemetery\Registrar\Domain\Model\GeoPosition\GeoPosition;
use DataFixtures\NaturalPerson\NaturalPersonProvider;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumNicheProvider
{
    public static function getColumbariumNicheA(): ColumbariumNiche
    {
        $id               = new ColumbariumNicheId('CN001');
        $columbariumId    = (ColumbariumProvider::getColumbariumA())->id();
        $rowInColumbarium = new RowInColumbarium(1);
        $nicheNumber      = new ColumbariumNicheNumber('001');

        return new ColumbariumNiche($id, $columbariumId, $rowInColumbarium, $nicheNumber);
    }

    public static function getColumbariumNicheB(): ColumbariumNiche
    {
        $id               = new ColumbariumNicheId('CN002');
        $columbariumId    = (ColumbariumProvider::getColumbariumB())->id();
        $rowInColumbarium = new RowInColumbarium(2);
        $nicheNumber      = new ColumbariumNicheNumber('002');
        $geoPosition      = new GeoPosition(new Coordinates('+54.95035712', '082.7925200'), new Error('0.5'));
        $personInCharge   = NaturalPersonProvider::getNaturalPersonG();

        return (new ColumbariumNiche($id, $columbariumId, $rowInColumbarium, $nicheNumber))
            ->setGeoPosition($geoPosition)
            ->assignPersonInCharge($personInCharge);
    }

    public static function getColumbariumNicheC(): ColumbariumNiche
    {
        $id               = new ColumbariumNicheId('CN003');
        $columbariumId    = (ColumbariumProvider::getColumbariumC())->id();
        $rowInColumbarium = new RowInColumbarium(3);
        $nicheNumber      = new ColumbariumNicheNumber('003');
        $geoPosition      = new GeoPosition(new Coordinates('-050.9500', '-179.7972252'), null);

        return (new ColumbariumNiche($id, $columbariumId, $rowInColumbarium, $nicheNumber))
            ->setGeoPosition($geoPosition);
    }

    public static function getColumbariumNicheD(): ColumbariumNiche
    {
        $id               = new ColumbariumNicheId('CN004');
        $columbariumId    = (ColumbariumProvider::getColumbariumD())->id();
        $rowInColumbarium = new RowInColumbarium(4);
        $nicheNumber      = new ColumbariumNicheNumber('004');

        return new ColumbariumNiche($id, $columbariumId, $rowInColumbarium, $nicheNumber);
    }

    public static function getColumbariumNicheE(): ColumbariumNiche
    {
        $id               = new ColumbariumNicheId('CN005');
        $columbariumId    = (ColumbariumProvider::getColumbariumD())->id();
        $rowInColumbarium = new RowInColumbarium(5);
        $nicheNumber      = new ColumbariumNicheNumber('006');

        return new ColumbariumNiche($id, $columbariumId, $rowInColumbarium, $nicheNumber);
    }

    public static function getColumbariumNicheF(): ColumbariumNiche
    {
        $id               = new ColumbariumNicheId('CN006');
        $columbariumId    = (ColumbariumProvider::getColumbariumD())->id();
        $rowInColumbarium = new RowInColumbarium(7);
        $nicheNumber      = new ColumbariumNicheNumber('005');

        return new ColumbariumNiche($id, $columbariumId, $rowInColumbarium, $nicheNumber);
    }

    public static function getColumbariumNicheG(): ColumbariumNiche
    {
        $id               = new ColumbariumNicheId('CN007');
        $columbariumId    = (ColumbariumProvider::getColumbariumD())->id();
        $rowInColumbarium = new RowInColumbarium(7);
        $nicheNumber      = new ColumbariumNicheNumber('007');

        return new ColumbariumNiche($id, $columbariumId, $rowInColumbarium, $nicheNumber);
    }

    public static function getColumbariumNicheH(): ColumbariumNiche
    {
        $id               = new ColumbariumNicheId('CN008');
        $columbariumId    = (ColumbariumProvider::getColumbariumD())->id();
        $rowInColumbarium = new RowInColumbarium(5);
        $nicheNumber      = new ColumbariumNicheNumber('001');

        return new ColumbariumNiche($id, $columbariumId, $rowInColumbarium, $nicheNumber);
    }
}
