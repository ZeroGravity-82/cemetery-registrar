<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\Model\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\Model\GeoPosition\Error;
use Cemetery\Registrar\Domain\Model\GeoPosition\GeoPosition;
use Cemetery\Registrar\Domain\Model\EntityFactory;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumNicheFactory extends EntityFactory
{
    /**
     * @param string|null $columbariumId
     * @param int|null    $rowInColumbarium
     * @param string|null $nicheNumber
     * @param string|null $geoPositionLatitude
     * @param string|null $geoPositionLongitude
     * @param string|null $geoPositionError
     *
     * @return ColumbariumNiche
     */
    public function create(
        ?string  $columbariumId,
        ?int     $rowInColumbarium,
        ?string  $nicheNumber,
        ?string  $geoPositionLatitude,
        ?string  $geoPositionLongitude,
        ?string  $geoPositionError,
    ): ColumbariumNiche {
        $columbariumId          = new ColumbariumId((string) $columbariumId);
        $rowInColumbarium       = new RowInColumbarium((int) $rowInColumbarium);
        $nicheNumber            = new ColumbariumNicheNumber((string) $nicheNumber);
        $geoPositionCoordinates = $geoPositionLatitude !== null && $geoPositionLongitude !== null
            ? new Coordinates($geoPositionLatitude, $geoPositionLongitude)
            : null;
        $geoPositionError = $geoPositionError !== null
            ? new Error($geoPositionError)
            : null;
        $geoPosition = $geoPositionCoordinates !== null
            ? new GeoPosition($geoPositionCoordinates, $geoPositionError)
            : null;

        return (new ColumbariumNiche(
            new ColumbariumNicheId($this->identityGenerator->getNextIdentity()),
            $columbariumId,
            $rowInColumbarium,
            $nicheNumber,
        ))
            ->setGeoPosition($geoPosition);
    }
}
