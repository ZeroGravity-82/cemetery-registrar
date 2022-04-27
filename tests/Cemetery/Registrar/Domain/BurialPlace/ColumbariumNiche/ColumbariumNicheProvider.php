<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumId;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheNumber;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\RowInColumbarium;
use Cemetery\Registrar\Domain\GeoPosition\Accuracy;
use Cemetery\Registrar\Domain\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\GeoPosition\GeoPosition;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class ColumbariumNicheProvider
{
    public static function getColumbariumNicheA(): ColumbariumNiche
    {
        $id               = new ColumbariumNicheId('CN001');
        $columbariumId    = new ColumbariumId('C001');
        $rowInColumbarium = new RowInColumbarium(4);
        $nicheNumber      = new ColumbariumNicheNumber('001');

        return new ColumbariumNiche($id, $columbariumId, $rowInColumbarium, $nicheNumber);
    }

    public static function getColumbariumNicheB(): ColumbariumNiche
    {
        $id               = new ColumbariumNicheId('CN002');
        $columbariumId    = new ColumbariumId('C002');
        $rowInColumbarium = new RowInColumbarium(5);
        $nicheNumber      = new ColumbariumNicheNumber('002');
        $geoPosition      = new GeoPosition(new Coordinates('54.950357', '82.7972252'), new Accuracy('0.5'));

        return (new ColumbariumNiche($id, $columbariumId, $rowInColumbarium, $nicheNumber))
            ->setGeoPosition($geoPosition);
    }

    public static function getColumbariumNicheC(): ColumbariumNiche
    {
        $id               = new ColumbariumNicheId('CN003');
        $columbariumId    = new ColumbariumId('C003');
        $rowInColumbarium = new RowInColumbarium(3);
        $nicheNumber      = new ColumbariumNicheNumber('003');
        $geoPosition      = new GeoPosition(new Coordinates('50.950357', '80.7972252'), null);

        return (new ColumbariumNiche($id, $columbariumId, $rowInColumbarium, $nicheNumber))
            ->setGeoPosition($geoPosition);
    }

    public static function getColumbariumNicheD(): ColumbariumNiche
    {
        $id               = new ColumbariumNicheId('CN004');
        $columbariumId    = new ColumbariumId('C004');
        $rowInColumbarium = new RowInColumbarium(2);
        $nicheNumber      = new ColumbariumNicheNumber('004');

        return new ColumbariumNiche($id, $columbariumId, $rowInColumbarium, $nicheNumber);
    }
}