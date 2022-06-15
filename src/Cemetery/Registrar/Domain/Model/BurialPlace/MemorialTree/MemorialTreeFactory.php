<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree;

use Cemetery\Registrar\Domain\Model\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\Model\GeoPosition\Error;
use Cemetery\Registrar\Domain\Model\GeoPosition\GeoPosition;
use Cemetery\Registrar\Domain\Model\EntityFactory;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class MemorialTreeFactory extends EntityFactory
{
    /**
     * @param string|null $treeNumber
     * @param string|null $geoPositionLatitude
     * @param string|null $geoPositionLongitude
     * @param string|null $geoPositionError
     *
     * @return MemorialTree
     */
    public function create(
        ?string $treeNumber,
        ?string $geoPositionLatitude,
        ?string $geoPositionLongitude,
        ?string $geoPositionError,
    ): MemorialTree {
        $treeNumber             = new MemorialTreeNumber((string) $treeNumber);
        $geoPositionCoordinates = $geoPositionLatitude !== null && $geoPositionLongitude !== null
            ? new Coordinates($geoPositionLatitude, $geoPositionLongitude)
            : null;
        $geoPositionError = $geoPositionError !== null
            ? new Error($geoPositionError)
            : null;
        $geoPosition = $geoPositionCoordinates !== null
            ? new GeoPosition($geoPositionCoordinates, $geoPositionError)
            : null;

        return (new MemorialTree(
            new MemorialTreeId($this->identityGenerator->getNextIdentity()),
            $treeNumber,
        ))
            ->setGeoPosition($geoPosition);
    }
}