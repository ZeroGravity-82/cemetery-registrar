<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\BurialPlace\CemeteryBlockId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteSize;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\PositionInRow;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\RowInBlock;
use Cemetery\Registrar\Domain\GeoPosition\Accuracy;
use Cemetery\Registrar\Domain\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\GeoPosition\GeoPosition;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class GraveSiteProvider
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
        $geoPosition     = new GeoPosition(new Coordinates('54.950357', '82.7972252'), new Accuracy('0.5'));

        return (new GraveSite($id, $cemeteryBlockId, $rowInBlock))
            ->setPositionInRow($positionInRow)
            ->setGeoPosition($geoPosition);
    }

    public static function getGraveSiteC(): GraveSite
    {
        $id              = new GraveSiteId('GS003');
        $cemeteryBlockId = new CemeteryBlockId('CB003');
        $rowInBlock      = new RowInBlock(1);
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
}
