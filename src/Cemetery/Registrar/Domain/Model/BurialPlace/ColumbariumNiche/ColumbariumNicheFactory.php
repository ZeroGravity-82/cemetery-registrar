<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\Model\GeoPosition\Error;
use Cemetery\Registrar\Domain\Model\GeoPosition\GeoPosition;
use Cemetery\Registrar\Domain\Model\AbstractEntityFactory;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumNicheFactory extends AbstractEntityFactory
{
    /**
     * @throws Exception when generating an invalid columbarium niche ID
     * @throws Exception when the columbarium ID is empty
     * @throws Exception when the row in columbarium value is invalid
     * @throws Exception when the niche number is invalid
     * @throws Exception when the geo position latitude value (if any) is invalid
     * @throws Exception when the geo position longitude value (if any) is invalid
     * @throws Exception when the geo position error value (if any) is invalid
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
