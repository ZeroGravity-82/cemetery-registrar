<?php

declare(strict_types=1);

namespace DataFixtures\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\Columbarium;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumName;
use Cemetery\Registrar\Domain\Model\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\Model\GeoPosition\Error;
use Cemetery\Registrar\Domain\Model\GeoPosition\GeoPosition;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumProvider
{
    public static function getColumbariumA(): Columbarium
    {
        $id   = new ColumbariumId('C001');
        $name = new ColumbariumName('западный');

        return new Columbarium($id, $name);
    }

    public static function getColumbariumB(): Columbarium
    {
        $id          = new ColumbariumId('C002');
        $name        = new ColumbariumName('южный');
        $geoPosition = new GeoPosition(new Coordinates('+54.95035712', '082.7925200'), new Error('0.5'));

        return (new Columbarium($id, $name))
            ->setGeoPosition($geoPosition);
    }

    public static function getColumbariumC(): Columbarium
    {
        $id          = new ColumbariumId('C003');
        $name        = new ColumbariumName('восточный');
        $geoPosition = new GeoPosition(new Coordinates('-050.9500', '-179.7972252'), null);

        return (new Columbarium($id, $name))
            ->setGeoPosition($geoPosition);
    }

    public static function getColumbariumD(): Columbarium
    {
        $id   = new ColumbariumId('C004');
        $name = new ColumbariumName('северный');

        return new Columbarium($id, $name);
    }
}
