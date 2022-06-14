<?php

declare(strict_types=1);

namespace DataFixtures\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteSize;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\PositionInRow;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\RowInBlock;
use Cemetery\Registrar\Domain\Model\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\Model\GeoPosition\Error;
use Cemetery\Registrar\Domain\Model\GeoPosition\GeoPosition;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteProvider
{
    public static function getGraveSiteA(): GraveSite
    {
        $id              = new GraveSiteId('GS001');
        $cemeteryBlockId = new CemeteryBlockId('CB001');
        $rowInBlock      = new RowInBlock(1);

        return new GraveSite($id, $cemeteryBlockId, $rowInBlock);
    }

    public static function getGraveSiteB(): GraveSite
    {
        $id              = new GraveSiteId('GS002');
        $cemeteryBlockId = new CemeteryBlockId('CB002');
        $rowInBlock      = new RowInBlock(3);
        $positionInRow   = new PositionInRow(4);
        $geoPosition     = new GeoPosition(new Coordinates('54.950357', '82.7972252'), new Error('0.5'));

        return (new GraveSite($id, $cemeteryBlockId, $rowInBlock))
            ->setPositionInRow($positionInRow)
            ->setGeoPosition($geoPosition);
    }

    public static function getGraveSiteC(): GraveSite
    {
        $id              = new GraveSiteId('GS003');
        $cemeteryBlockId = new CemeteryBlockId('CB003');
        $rowInBlock      = new RowInBlock(7);
        $geoPosition     = new GeoPosition(new Coordinates('50.950357', '80.7972252'), null);
        $size            = new GraveSiteSize('2.5');

        return (new GraveSite($id, $cemeteryBlockId, $rowInBlock))
            ->setGeoPosition($geoPosition)
            ->setSize($size);
    }

    public static function getGraveSiteD(): GraveSite
    {
        $id              = new GraveSiteId('GS004');
        $cemeteryBlockId = new CemeteryBlockId('CB004');
        $rowInBlock      = new RowInBlock(2);

        return new GraveSite($id, $cemeteryBlockId, $rowInBlock);
    }

    public static function getGraveSiteE(): GraveSite
    {
        $id              = new GraveSiteId('GS005');
        $cemeteryBlockId = new CemeteryBlockId('CB004');
        $rowInBlock      = new RowInBlock(3);

        return new GraveSite($id, $cemeteryBlockId, $rowInBlock);
    }
}
