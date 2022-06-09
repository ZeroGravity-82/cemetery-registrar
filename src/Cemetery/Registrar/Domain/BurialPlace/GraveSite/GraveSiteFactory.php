<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\EntityFactory;
use Cemetery\Registrar\Domain\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\GeoPosition\Error;
use Cemetery\Registrar\Domain\GeoPosition\GeoPosition;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteFactory extends EntityFactory
{
    /**
     * @param string|null $cemeteryBlockId
     * @param int|null    $rowInBlock
     * @param int|null    $positionInRow
     * @param string|null $geoPositionLatitude
     * @param string|null $geoPositionLongitude
     * @param string|null $geoPositionError
     * @param string|null $size
     *
     * @return GraveSite
     */
    public function create(
        ?string $cemeteryBlockId,
        ?int    $rowInBlock,
        ?int    $positionInRow,
        ?string $geoPositionLatitude,
        ?string $geoPositionLongitude,
        ?string $geoPositionError,
        ?string $size,
    ): GraveSite {
        $cemeteryBlockId        = new CemeteryBlockId((string) $cemeteryBlockId);
        $rowInBlock             = new RowInBlock((int) $rowInBlock);
        $positionInRow          = $positionInRow !== null ? new PositionInRow($positionInRow) : null;
        $geoPositionCoordinates = $geoPositionLatitude !== null && $geoPositionLongitude !== null
            ? new Coordinates($geoPositionLatitude, $geoPositionLongitude)
            : null;
        $geoPositionError = $geoPositionError !== null
            ? new Error($geoPositionError)
            : null;
        $geoPosition = $geoPositionCoordinates !== null
            ? new GeoPosition($geoPositionCoordinates, $geoPositionError)
            : null;
        $size = $size !== null ? new GraveSiteSize($size) : null;

        return (new GraveSite(
            new GraveSiteId($this->identityGenerator->getNextIdentity()),
            $cemeteryBlockId,
            $rowInBlock,
        ))
            ->setPositionInRow($positionInRow)
            ->setGeoPosition($geoPosition)
            ->setSize($size);
    }
}
