<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\Model\GeoPosition\Error;
use Cemetery\Registrar\Domain\Model\GeoPosition\GeoPosition;
use Cemetery\Registrar\Domain\Model\EntityFactory;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteFactory extends EntityFactory
{
    /**
     * @throws Exception when generating an invalid grave site ID
     * @throws Exception when the cemetery block ID is empty
     * @throws Exception when the row in block value is invalid
     * @throws Exception when the position in row value (if any) is invalid
     * @throws Exception when the geo position latitude value (if any) is invalid
     * @throws Exception when the geo position longitude value (if any) is invalid
     * @throws Exception when the geo position error value (if any) is invalid
     * @throws Exception when the grave site size value (if any) is invalid
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
