<?php

declare(strict_types=1);

namespace DataFixtures\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteSize;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\PositionInRow;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\RowInBlock;
use Cemetery\Registrar\Domain\Model\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\Model\GeoPosition\Error;
use Cemetery\Registrar\Domain\Model\GeoPosition\GeoPosition;
use DataFixtures\NaturalPerson\NaturalPersonProvider;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteProvider
{
    public static function getGraveSiteA(): GraveSite
    {
        $id              = new GraveSiteId('GS001');
        $cemeteryBlockId = (CemeteryBlockProvider::getCemeteryBlockA())->id();
        $rowInBlock      = new RowInBlock(1);

        return new GraveSite($id, $cemeteryBlockId, $rowInBlock);
    }

    public static function getGraveSiteB(): GraveSite
    {
        $id              = new GraveSiteId('GS002');
        $cemeteryBlockId = (CemeteryBlockProvider::getCemeteryBlockB())->id();
        $rowInBlock      = new RowInBlock(3);
        $positionInRow   = new PositionInRow(4);
        $geoPosition     = new GeoPosition(new Coordinates('54.950357', '82.7972252'), new Error('0.5'));
        $personInCharge  = NaturalPersonProvider::getNaturalPersonH();

        return (new GraveSite($id, $cemeteryBlockId, $rowInBlock))
            ->setPositionInRow($positionInRow)
            ->setGeoPosition($geoPosition)
            ->assignPersonInCharge($personInCharge);
    }

    public static function getGraveSiteC(): GraveSite
    {
        $id              = new GraveSiteId('GS003');
        $cemeteryBlockId = (CemeteryBlockProvider::getCemeteryBlockC())->id();
        $rowInBlock      = new RowInBlock(7);
        $geoPosition     = new GeoPosition(new Coordinates('50.950357', '80.7972252'), null);
        $size            = new GraveSiteSize('2.5');
        $personInCharge  = NaturalPersonProvider::getNaturalPersonG();

        return (new GraveSite($id, $cemeteryBlockId, $rowInBlock))
            ->setGeoPosition($geoPosition)
            ->setSize($size)
            ->assignPersonInCharge($personInCharge);
    }

    public static function getGraveSiteD(): GraveSite
    {
        $id              = new GraveSiteId('GS004');
        $cemeteryBlockId = (CemeteryBlockProvider::getCemeteryBlockD())->id();
        $rowInBlock      = new RowInBlock(2);
        $positionInRow   = new PositionInRow(4);

        return (new GraveSite($id, $cemeteryBlockId, $rowInBlock))
            ->setPositionInRow($positionInRow);
    }

    public static function getGraveSiteE(): GraveSite
    {
        $id              = new GraveSiteId('GS005');
        $cemeteryBlockId = (CemeteryBlockProvider::getCemeteryBlockD())->id();
        $rowInBlock      = new RowInBlock(3);
        $positionInRow   = new PositionInRow(11);

        return (new GraveSite($id, $cemeteryBlockId, $rowInBlock))
            ->setPositionInRow($positionInRow);
    }

    public static function getGraveSiteF(): GraveSite
    {
        $id              = new GraveSiteId('GS006');
        $cemeteryBlockId = (CemeteryBlockProvider::getCemeteryBlockD())->id();
        $rowInBlock      = new RowInBlock(3);
        $positionInRow   = new PositionInRow(10);

        return (new GraveSite($id, $cemeteryBlockId, $rowInBlock))
            ->setPositionInRow($positionInRow);
    }

    public static function getGraveSiteG(): GraveSite
    {
        $id              = new GraveSiteId('GS007');
        $cemeteryBlockId = (CemeteryBlockProvider::getCemeteryBlockB())->id();
        $rowInBlock      = new RowInBlock(3);
        $positionInRow   = new PositionInRow(5);

        return (new GraveSite($id, $cemeteryBlockId, $rowInBlock))
            ->setPositionInRow($positionInRow);
    }

    public static function getGraveSiteH(): GraveSite
    {
        $id              = new GraveSiteId('GS008');
        $cemeteryBlockId = (CemeteryBlockProvider::getCemeteryBlockB())->id();
        $rowInBlock      = new RowInBlock(3);
        $positionInRow   = new PositionInRow(6);

        return (new GraveSite($id, $cemeteryBlockId, $rowInBlock))
            ->setPositionInRow($positionInRow);
    }

    public static function getGraveSiteI(): GraveSite
    {
        $id              = new GraveSiteId('GS009');
        $cemeteryBlockId = (CemeteryBlockProvider::getCemeteryBlockB())->id();
        $rowInBlock      = new RowInBlock(3);
        $positionInRow   = new PositionInRow(7);
        $size            = new GraveSiteSize('3.5');

        return (new GraveSite($id, $cemeteryBlockId, $rowInBlock))
            ->setPositionInRow($positionInRow)
            ->setSize($size);
    }
}
