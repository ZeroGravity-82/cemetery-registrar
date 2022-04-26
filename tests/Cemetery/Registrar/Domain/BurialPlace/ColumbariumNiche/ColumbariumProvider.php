<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\Columbarium;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumId;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumName;
use Cemetery\Registrar\Domain\GeoPosition\Accuracy;
use Cemetery\Registrar\Domain\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\GeoPosition\GeoPosition;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class ColumbariumProvider
{
    public static function getColumbariumA(): Columbarium
    {
        $id   = new ColumbariumId('C001');
        $name = new ColumbariumName('западный колумбарий');

        return new Columbarium($id, $name);
    }

    public static function getColumbariumB(): Columbarium
    {
        $id          = new ColumbariumId('C002');
        $name        = new ColumbariumName('южный колумбарий');
        $geoPosition = new GeoPosition(new Coordinates('54.950357', '82.7972252'), new Accuracy('0.5'));

        return (new Columbarium($id, $name))
            ->setGeoPosition($geoPosition);
    }

    public static function getColumbariumC(): Columbarium
    {
        $id          = new ColumbariumId('C003');
        $name        = new ColumbariumName('восточный колумбарий');
        $geoPosition = new GeoPosition(new Coordinates('50.950357', '80.7972252'), null);

        return (new Columbarium($id, $name))
            ->setGeoPosition($geoPosition);
    }

    public static function getColumbariumD(): Columbarium
    {
        $id   = new ColumbariumId('C004');
        $name = new ColumbariumName('северный колумбарий');

        return new Columbarium($id, $name);
    }
}
