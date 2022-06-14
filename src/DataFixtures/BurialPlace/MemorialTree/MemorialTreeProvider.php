<?php

declare(strict_types=1);

namespace DataFixtures\BurialPlace\MemorialTree;

use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTree;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeNumber;
use Cemetery\Registrar\Domain\Model\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\Model\GeoPosition\Error;
use Cemetery\Registrar\Domain\Model\GeoPosition\GeoPosition;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class MemorialTreeProvider
{
    public static function getMemorialTreeA(): MemorialTree
    {
        $id         = new MemorialTreeId('MT001');
        $treeNumber = new MemorialTreeNumber('001');

        return new MemorialTree($id, $treeNumber);
    }

    public static function getMemorialTreeB(): MemorialTree
    {
        $id          = new MemorialTreeId('MT002');
        $treeNumber  = new MemorialTreeNumber('002');
        $geoPosition = new GeoPosition(new Coordinates('54.950457', '82.7972252'), new Error('0.5'));

        return (new MemorialTree($id, $treeNumber))
            ->setGeoPosition($geoPosition);
    }

    public static function getMemorialTreeC(): MemorialTree
    {
        $id          = new MemorialTreeId('MT003');
        $treeNumber  = new MemorialTreeNumber('003');
        $geoPosition = new GeoPosition(new Coordinates('50.950357', '80.7972252'), null);

        return (new MemorialTree($id, $treeNumber))
            ->setGeoPosition($geoPosition);
    }

    public static function getMemorialTreeD(): MemorialTree
    {
        $id         = new MemorialTreeId('MT004');
        $treeNumber = new MemorialTreeNumber('004');

        return new MemorialTree($id, $treeNumber);
    }
}
